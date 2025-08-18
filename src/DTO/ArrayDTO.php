<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO;

class ArrayDTO
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // Доступ к данным как к свойствам
    public function __get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    // Получить исходный массив
    public function toArray(): array
    {
        return $this->data;
    }
}
