<?php
declare(strict_types=1);

namespace Apd\Trenergy\Enums\Consumers;

enum PaymentPeriod: int
{
    case fifteenMinutes = 15;
    case hour = 60;
    case eightHours = 480;
    case day = 1440;

    /**
     * Возвращает все значения Enum через запятую
     */
    public static function getValuesAsString(): string
    {
        return implode(', ', array_column(self::cases(), 'value'));
    }
}
