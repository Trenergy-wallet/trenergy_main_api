<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;

class ConsumptionStatDTO extends BaseDTO
{
    public string $date;
    public float $resource_amount;
    public float $trx_price;
}
