<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Withdrawals;

use Apd\Trenergy\DTO\BaseDTO;

class GetWithdrawalsDTO extends BaseDTO
{
    public int $id;
    public float $trx_amount;
    public string $status;
    public string $address;
    public string $created_at;
    public string $updated_at;
}
