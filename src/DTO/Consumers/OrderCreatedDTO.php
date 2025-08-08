<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;

class OrderCreatedDTO extends BaseDTO
{
    public int $id;
    public string $name;
    public string $address;
    public float $resource_amount;
    public float $desired_resource_amount;
    public int $creation_type;
    public int $consumption_type;
    public ?int $recharge_type;
    public int $payment_period;
    public bool $auto_renewal;
    public bool $is_active;
    public bool $activation_queue;
    public mixed $order;
    public ?string $webhook_url;
    public string $created_at;
    public string $updated_at;
}
