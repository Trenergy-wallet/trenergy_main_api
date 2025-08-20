<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\ArrayDTO;
use Apd\Trenergy\DTO\BaseDTO;
use Apd\Trenergy\Enums\Consumers\PaymentPeriod;

class ConsumerSummaryDTO extends BaseDTO
{
    public readonly float $balance;
    public readonly float $credit_limit;
    public readonly int $total_count;
    public readonly float $total_energy_consumption;
    public readonly float $total_received_energy;
    public readonly int $active_count;
    public readonly float $active_energy_consumption;
    public readonly float $normal_energy_unit_price;
    public readonly float $trenergy_energy_unit_price;
    public readonly float $aml_price_usd;
    public readonly float $daily_expenses_avg;
    public PeriodPricesSunDTO $period_prices_sun;

    protected array $nestedDtos = [
        'period_prices_sun' => PeriodPricesSunDTO::class,
    ];
}
