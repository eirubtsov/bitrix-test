<?php

declare(strict_types=1);

namespace Weather\Forecast\Collections;

use ArrayIterator;
use Weather\Forecast\Contracts\Collection\TypedCollectionInterface;
use InvalidArgumentException;

/**
 * Базовый класс типизированной коллекции объектов.
 * Иммутабельная семантика: все «изменяющие» методы возвращают новый экземпляр.
 *
 * @template TItem Тип элемента коллекции (доменный объект/DTO)
 * @implements TypedCollectionInterface<TItem>
 */
abstract class AbstractTypedCollection implements TypedCollectionInterface
{
    /**
     * Внутреннее хранилище элементов.
     *
     * @var array<int|string, TItem>
     */
    protected array $items;

    /**
     * @param iterable<int|string, TItem> $items
     */
    final public function __construct(iterable $items = [])
    {
        $array = is_array($items) ? $items : iterator_to_array($items, true);
        $this->assertItems($array);
        $this->items = $array;
    }

    /**
     * Ожидаемый FQCN типа элемента.
     *
     * @return class-string
     */
    abstract protected function elementType(): string;

    /**
     * Проверка всех элементов на соответствие типу.
     *
     * @param array<int|string, mixed> $items
     */
    protected function assertItems(array $items): void
    {
        $expected = $this->elementType();

        foreach ($items as $index => $item) {
            if (!$item instanceof $expected) {
                $type = is_object($item) ? $item::class : gettype($item);
                throw new InvalidArgumentException(
                    "Элемент '{$index}' должен быть экземпляром {$expected}, а передан {$type}."
                );
            }
        }
    }

    /**
     * Фабрика нового экземпляра того же класса (для иммутабельных операций).
     *
     * @param array<int|string, TItem> $items
     * @return static
     */
    protected function new(array $items): static
    {
        return new static($items);
    }

    // ===== Статические фабрики =====

    /**
     * Сформировать коллекцию из набора элементов.
     *
     * @param iterable<int|string, TItem> $items
     * @return static
     */
    public static function from(iterable $items): static
    {
        return new static($items);
    }

    /**
     * Индексировать элементы по ключу, вычисленному селектором.
     *
     * @param iterable<int|string, TItem> $items
     * @param callable(TItem): int|string  $keySelector
     * @return static
     */
    public static function keyBy(iterable $items, callable $keySelector): static
    {
        $buffer = [];
        foreach ($items as $item) {
            $buffer[$keySelector($item)] = $item;
        }

        return new static($buffer);
    }

    // ===== Read-only =====

    /**
     * Вернуть внутреннее хранилище без преобразований (RAW).
     *
     * @return array<int|string, TItem>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Признак пустоты коллекции.
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Первый элемент или null.
     *
     * @return TItem|null
     */
    public function first(): mixed
    {
        return $this->items[array_key_first($this->items)] ?? null;
    }

    // ===== Transform (immutability) =====

    /**
     * Отобразить элементы коллекции.
     *
     * @param callable(TItem,int|string):TItem $mapper
     * @return static
     */
    public function map(callable $mapper): static
    {
        $mapped = [];
        foreach ($this->items as $key => $value) {
            /** @var TItem $result */
            $result = $mapper($value, $key);
            $mapped[$key] = $result;
        }

        $this->assertItems($mapped);
        return $this->new($mapped);
    }

    /**
     * Отфильтровать элементы коллекции.
     *
     * @param null|callable(TItem,int|string):bool $predicate
     * @return static
     */
    public function filter(?callable $predicate = null): static
    {
        $predicate ??= static fn ($v): bool => (bool) $v;

        $filtered = array_filter(
            $this->items,
            static fn ($value, $key): bool => $predicate($value, $key),
            ARRAY_FILTER_USE_BOTH
        );

        /** @var array<int|string, TItem> $filtered */
        return $this->new($filtered);
    }

    /**
     * Свернуть коллекцию к одному значению.
     *
     * @param callable(mixed,TItem,int|string):mixed $reducer
     */
    public function reduce(callable $reducer, mixed $initial = null): mixed
    {
        $acc = $initial;
        foreach ($this->items as $key => $value) {
            $acc = $reducer($acc, $value, $key);
        }

        return $acc;
    }

    // ===== "Mutable" (иммутабельно по факту) =====

    /**
     * Добавить элемент в конец. Возвращает НОВУЮ коллекцию.
     *
     * @param TItem $item
     * @return static
     */
    public function push(mixed $item): static
    {
        $expected = $this->elementType();
        if (!$item instanceof $expected) {
            $type = is_object($item) ? $item::class : gettype($item);
            throw new InvalidArgumentException("Элемент должен быть экземпляром {$expected}, а передан {$type}.");
        }

        $clone = $this->items;
        $clone[] = $item;

        return $this->new($clone);
    }

    // ===== Сериализация =====

    /**
     * Хук сериализации одного элемента при преобразовании коллекции в массив/JSON.
     * Переопредели в наследнике, чтобы, например, вызывать $dto->toArray().
     *
     * @param TItem      $item Элемент коллекции
     * @param int|string $key  Ключ элемента
     * @return mixed            Значение, которое попадёт в итоговый массив
     */
    protected function serializeItem(mixed $item, int|string $key): mixed
    {
        // По умолчанию — возвращаем элемент «как есть».
        return $item;
    }

    /**
     * Коллекция в массив с применением хука serializeItem().
     * Если нужен «сырой» массив объектов — используй all().
     *
     * @return array<int|string, mixed>
     */
    public function toArray(): array
    {
        $out = [];
        foreach ($this->items as $key => $item) {
            $out[$key] = $this->serializeItem($item, $key);
        }

        return $out;
    }

    /**
     * Итератор по элементам коллекции.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Количество элементов.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Сериализация в JSON использует результат toArray().
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Представление коллекции в JSON-строку.
     */
    public function toJson(int $flags = 0): string
    {
        $json = json_encode(
            $this->jsonSerialize(),
            $flags | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return $json === false ? '[]' : $json;
    }
}
