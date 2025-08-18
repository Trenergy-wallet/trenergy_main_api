<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO;

class BaseDTO
{
    public function __construct(?array $data)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                if (property_exists($this, (string)$key)) {
                    $this->{$key} = $this->castValue($key, $value);
                }
            }
        }
    }

    protected function castValue(string $key, $value)
    {
        // Если есть тип для свойства, можно кастовать
        if (isset($this->casts[$key])) {
            return $this->castToType($value, $this->casts[$key]);
        }

        // Рекурсивное создание DTO для вложенных массивов
        if (is_array($value)) {
            return $this->createNestedDto($key, $value);
        }

        return $value;
    }

    protected function castToType($value, string $type)
    {
        switch ($type) {
            case 'int':
                return (int)$value;
            case 'string':
                return (string)$value;
            case 'bool':
                return (bool)$value;
            case 'float':
                return (float)$value;
            default:
                return $value;
        }
    }

    protected function createNestedDto(string $key, array $data)
    {
        // If specified a DTO class for nested objects
        if (isset($this->nestedDtos[$key])) {
            $dtoClass = $this->nestedDtos[$key];

            // If this is an array of items that should each be a DTO
            if (array_keys($data) === range(0, count($data) - 1)) {
                $result = [];
                foreach ($data as $item) {
                    $result[] = new $dtoClass($item);
                }
                return $result;
            }

            return new $dtoClass($data);
        }

        // If associative array, convert to ArrayDTO
        if (array_keys($data) !== range(0, count($data) - 1)) {
            return new ArrayDTO($data);
        }

        return $data;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
