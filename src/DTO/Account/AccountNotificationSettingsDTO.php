<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Account;

use Apd\Trenergy\DTO\BaseDTO;

class AccountNotificationSettingsDTO extends BaseDTO
{
    public string $name;
    public bool $value;
    public int $id;
}
