<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;
use Apd\Trenergy\Enums\Consumers\PaymentPeriod;

class PeriodPricesSunDTO extends BaseDTO
{
    /** @var array<PaymentPeriod, int> Цены для каждого периода */
    public array $paymentPeriods;

    /** @var array<string, string> */
    protected array $casts = [
        'prices' => 'array', // Указываем, что prices должен быть массивом
    ];

    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        // Преобразуем входные данные в структуру с PaymentPeriod
        $this->paymentPeriods = $this->mapPricesToEnum($data ?? []);
    }

    /**
     * Преобразует массив вида ["15" => 76, "60" => 100] в [PaymentPeriod::fifteenMinutes => 76, ...]
     * @param array<string, int> $rawPrices
     * @return array<PaymentPeriod, int>
     */
    protected function mapPricesToEnum(array $rawPrices): array
    {
        $mappedPrices = [];

        foreach ($rawPrices as $minutes => $price) {
            $period = PaymentPeriod::tryFrom((int)$minutes);
            if ($period !== null) {
                $mappedPrices[$period->value] = $price;
            }
        }

        return $mappedPrices;
    }
}
