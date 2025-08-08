<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;

class ConsumerDTO extends BaseDTO
{
    public readonly int $id;
    public readonly string $name;
    public readonly string $address;
    public readonly int $resource_amount;
    public readonly int $creation_type;
    public readonly int $consumption_type;
    public readonly int $payment_period;
    public readonly bool $auto_renewal;
    public readonly ?array $resource_consumptions;
    public readonly bool $is_active;
    public readonly OrderDTO $order;
    public readonly BoostOrderDTO $boostOrder;
    public readonly ?string $webhook_url;
    public readonly string $created_at;
    public readonly string $updated_at;

    protected function castValue(string $key, $value)
    {
        // Специальная обработка для вложенных объектов
        return match($key) {
            'id', 'resource_amount', 'creation_type', 'consumption_type', 'payment_period' => (int) $value,
            'auto_renewal', 'is_active' => (bool) $value,
            'order' => new OrderDTO($value),
            'boostOrder' => new BoostOrderDTO($value),
            'address', 'name', 'created_at', 'updated_at' => (string)$value,
            default => parent::castValue($key, $value)
        };
    }
}
