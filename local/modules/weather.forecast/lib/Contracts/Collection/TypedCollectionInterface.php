<?php

declare(strict_types=1);

namespace Weather\Forecast\Contracts\Collection;

use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * Контракт типизированной коллекции объектов с иммутабельной семантикой.
 *
 * @template TItem Тип элемента коллекции
 */
interface TypedCollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{
    /**
     * Вернуть внутренний массив элементов.
     *
     * @return array<int|string, TItem>
     */
    public function all(): array;

    /**
     * Пустая ли коллекция.
     */
    public function isEmpty(): bool;

    /**
     * Первый элемент или null.
     *
     * @return TItem|null
     */
    public function first(): mixed;

    /**
     * Преобразовать элементы (map). Возвращает новый экземпляр.
     *
     * @param callable(TItem,int|string):TItem $mapper
     * @return static
     */
    public function map(callable $mapper): static;

    /**
     * Отфильтровать элементы. Возвращает новый экземпляр.
     *
     * @param null|callable(TItem,int|string):bool $predicate
     * @return static
     */
    public function filter(?callable $predicate = null): static;

    /**
     * Свернуть коллекцию к одному значению.
     *
     * @param callable(mixed,TItem,int|string):mixed $reducer
     */
    public function reduce(callable $reducer, mixed $initial = null): mixed;

    /**
     * Добавить элемент в конец. Возвращает новый экземпляр.
     *
     * @param TItem $item
     * @return static
     */
    public function push(mixed $item): static;

    /**
     * Представление в массив.
     *
     * @return array<int|string, TItem>
     */
    public function toArray(): array;

    /**
     * Представление в JSON-строку.
     */
    public function toJson(int $flags = 0): string;

    /**
     * Статическая фабрика: собрать коллекцию из iterable.
     *
     * @param iterable<int|string, TItem> $items
     * @return static
     */
    public static function from(iterable $items): static;

    /**
     * Статическая фабрика: индексировать элементы по вычислённому ключу.
     *
     * @param iterable<int|string, TItem> $items
     * @param callable(TItem): int|string $keySelector
     * @return static
     */
    public static function keyBy(iterable $items, callable $keySelector): static;
}
