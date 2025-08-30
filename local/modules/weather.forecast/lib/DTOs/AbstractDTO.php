<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs;

use ReflectionUnionType;
use Weather\Forecast\Contracts\DTOs\ArraySerializableDTOInterface;
use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Weather\Forecast\DTOs\Weather\CurrentWeatherDTO;

/**
 * Базовый абстрактный класс DTO (Data Transfer Object) для быстрого создания объектов
 * из массивов данных и сериализации их обратно.
 *
 * Основные возможности:
 * ---------------------
 * 1. Создание экземпляра из массива (`fromArray()`):
 *    - Поддержка автоматического маппинга ключей входного массива на свойства конструктора.
 *    - Возможность задания собственной карты соответствий через `getFieldMap()`.
 *    - Приведение типов к типам, объявленным в сигнатуре конструктора.
 *    - Рекурсивная сборка вложенных DTO, если у них есть метод `fromArray()`.
 *    - Возможность трансформации значений через `getCustomTransformers()`.
 *
 * 2. Сериализация объекта в массив (`toArray()`):
 *    - Автоматическая обратная замена ключей по карте из `getFieldMap()`.
 *    - Кастомные сериализаторы для отдельных свойств через `getCustomSerializers()`.
 *    - Рекурсивная сериализация вложенных DTO и массивов DTO.
 *    - Пропуск свойств с `null` значениями.
 *
 * 3. Кастомизация поведения:
 *    - `protected static function getFieldMap(): array` — сопоставление "внешний ключ" => "имя параметра конструктора".
 *    - `protected static function getCustomTransformers(): array` — массив "имя параметра" => callable для преобразования входных значений.
 *    - `protected static function getCustomSerializers(): array` — массив "имя свойства" => callable для кастомной сериализации.
 */
abstract class AbstractDTO implements ArraySerializableDTOInterface
{
    /**
     * Кэш параметров конструктора для каждого класса
     * @var array
     */
    private static array $constructorParamsCache = [];

    /**
     * Маппинг: внешний ключ массива => внутреннее свойство
     *
     * @return array<string, string>
     */
    protected static function getFieldMap(): array
    {
        return [];
    }

    /**
     * Создание объекта DTO из массива
     *
     * @param array<string, mixed> $data
     * @return static
     * @throws ReflectionException
     */
    public static function fromArray(array $data): static
    {
        // Получаем карту полей (внешний ключ => внутреннее имя свойства конструктора)
        $map = static::getFieldMap();
        $transformers = static::getCustomTransformers();

        $class = static::class;
        if (!isset(self::$constructorParamsCache[$class])) {
            $refCtor = new \ReflectionMethod($class, '__construct');
            self::$constructorParamsCache[$class] = $refCtor->getParameters();
        }

        $args = [];
        foreach (self::$constructorParamsCache[$class] as $param) {
            $name = $param->getName();

            // Значение: приоритет — по карте; затем прямой ключ из $data
            $externalKey = array_search($name, $map, true);
            $hasMapped = $externalKey !== false && array_key_exists($externalKey, $data);
            $hasDirect = array_key_exists($name, $data);

            $value = $hasMapped ? $data[$externalKey]
                : ($hasDirect ? $data[$name] : null);

            // Трансформер — до приведения
            if (isset($transformers[$name])) {
                $value = ($transformers[$name])($value);
            }

            $type = $param->getType();

            $castScalarOrDto = static function (string $tn, mixed $val) {
                return match ($tn) {
                    'int' => (int)$val,
                    'float' => (float)$val,
                    'string' => (string)$val,
                    'bool' => filter_var($val, FILTER_VALIDATE_BOOLEAN) ?? false,
                    'array' => (array)$val,
                    default => self::castToDto($tn, $val),
                };
            };

            if ($type instanceof ReflectionNamedType) {
                $isNullable = $type->allowsNull();

                if ($value === null) {
                    if (!$isNullable && !$param->isDefaultValueAvailable()) {
                        throw new \InvalidArgumentException("Отсутствует обязательный параметр: {$name}");
                    }
                    $args[$name] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                    continue;
                }

                $args[$name] = $castScalarOrDto($type->getName(), $value);
                continue;
            }

            if ($type instanceof ReflectionUnionType) {
                $types = $type->getTypes();
                $isNullable = $type->allowsNull();

                if ($value === null) {
                    if (!$isNullable && !$param->isDefaultValueAvailable()) {
                        throw new InvalidArgumentException("Отсутствует обязательный параметр: {$name}");
                    }
                    $args[$name] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                    continue;
                }

                // Сначала — любой DTO-класс
                foreach ($types as $t) {
                    if ($t instanceof ReflectionNamedType) {
                        $tn = $t->getName();
                        if (class_exists($tn) && is_subclass_of($tn, self::class)) {
                            $args[$name] = is_array($value) ? $tn::fromArray($value) : $value;
                            continue 2;
                        }
                    }
                }
                // Затем — первая подходящая скалярщина/array
                foreach ($types as $t) {
                    if ($t instanceof ReflectionNamedType) {
                        $args[$name] = $castScalarOrDto($t->getName(), $value);
                        continue 2;
                    }
                }

                $args[$name] = $value;
                continue;
            }

            // Нет типа — как есть
            $args[$name] = $value;
        }

        return new static(...$args);
    }

    /**
     * Возвращает список трансформаторов значений для указанных свойств.
     *
     * @return array<string, callable>
     */
    protected static function getCustomTransformers(): array
    {
        return [];
    }

    /**
     * Возвращает список сериализаторов для отдельных свойств.
     *
     * @return array<string, callable>
     */
    protected static function getCustomSerializers(): array
    {
        return [];
    }

    /**
     * Если значение — массив, и класс существует, вызываем fromArray рекурсивно
     *
     * @param string $typeName
     * @param mixed $value
     * @return mixed
     * @throws ReflectionException
     */
    protected static function castToDto(string $typeName, mixed $value): mixed
    {
        // Базовые «не-классовые» типы — просто возвращаем.
        if (in_array($typeName, ['mixed', 'object', 'iterable'], true)) {
            return $value;
        }

        // Если тип не класс/интерфейс — это программная ошибка в вызове.
        if (!class_exists($typeName) && !interface_exists($typeName)) {
            return $value;
        }

        // Уже объект нужного типа — отдаём как есть.
        if ($value instanceof $typeName) {
            return $value;
        }

        // Если ожидается DTO (наследник AbstractDTO) и пришёл массив — собираем из массива.
        if (is_array($value) && is_subclass_of($typeName, self::class) && method_exists($typeName, 'fromArray')) {
            return $typeName::fromArray($value);
        }

        // В остальных случаях НЕ пытаемся создавать объект.
        return $value;
    }

    /**
     * Преобразование DTO в массив
     *
     * @return array<string, mixed>
     */
    public function toArray(bool $deleteNull = false): array
    {
        $map = static::getFieldMap();
        $reverseMap = array_flip($map);
        $serializers = static::getCustomSerializers();

        $result = [];

        foreach (get_object_vars($this) as $property => $value) {
            $externalKey = $reverseMap[$property] ?? $property;

            $value = $this->serializeValue($property, $value, $serializers, $deleteNull);

            if ($value !== null || !$deleteNull) {
                $result[$externalKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Сравнение объектов
     * @param AbstractDTO $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    /**
     * Обработка сериализации одного значения
     *
     * @param string $property
     * @param mixed $value
     * @param array<string, callable> $serializers
     * @param bool $deleteNull
     * @return mixed
     */
    protected function serializeValue(
        string $property,
        mixed $value,
        array $serializers,
        bool $deleteNull = false
    ): mixed {
        if (isset($serializers[$property]) && is_callable($serializers[$property])) {
            return $serializers[$property]($value);
        }

        if ($value instanceof self) {
            return $value->toArray($deleteNull);
        }

        if (is_array($value)) {
            return array_map(fn($item) => $item instanceof self ? $item->toArray() : $item, $value);
        }

        return $value;
    }
}
