<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Account;

use Apd\Trenergy\DTO\BaseDTO;

class AccountTopUpDTO extends BaseDTO
{
    public string $address;
    public string $qr_code;
    public int $time_left;
}
