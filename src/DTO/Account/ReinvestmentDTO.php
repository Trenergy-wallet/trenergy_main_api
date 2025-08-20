<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Account;

use Apd\Trenergy\DTO\BaseDTO;

class ReinvestmentDTO extends BaseDTO
{
    public bool $wallet;
    public bool $balance;
    public string $created_at;
    public string $updated_at;
}
