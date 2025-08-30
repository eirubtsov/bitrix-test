<?php

namespace Weather\Forecast\Contracts\DTOs;

interface ArraySerializableDTOInterface
{
    /**
     * Создание объекта DTO из массива
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static;

    /**
     * Преобразование DTO в массив
     * @param bool $deleteNull
     * @return array
     */
    public function toArray(bool $deleteNull = false): array;
}
