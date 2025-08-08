<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;
use Illuminate\Support\Collection;

class ConsumptionStatTotalDTO extends BaseDTO
{
    public float $total_trx_price;
    public float $total_resource_amount;
    public float $total_energy_balance_expenses;

    /** @var ConsumptionStatDTO[] */
    public array $items;

    protected array $nestedDtos = [
        'items' => ConsumptionStatDTO::class
    ];
}
