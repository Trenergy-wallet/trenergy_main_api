<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Account;

use Apd\Trenergy\DTO\BaseDTO;

class AccountSubscriptionDTO extends BaseDTO
{
    public ?string $created_at;
    public ?string $expires_at;
}
