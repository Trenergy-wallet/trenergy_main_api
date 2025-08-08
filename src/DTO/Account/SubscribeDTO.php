<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Account;

use Apd\Trenergy\DTO\BaseDTO;

class SubscribeDTO extends BaseDTO
{
    public string  $address;
    public string $qr_code;
    public int $time_left;
    public float $amount;
    public string  $currency;
}
