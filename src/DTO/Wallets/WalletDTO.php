<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Wallets;

use Apd\Trenergy\DTO\BaseDTO;

class WalletDTO extends BaseDTO
{
    public int $id;
    public string $address;
    public string $created_at;
    public string $updated_at;

}
