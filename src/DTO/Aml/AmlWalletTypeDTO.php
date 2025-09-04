<?php

namespace Apd\Trenergy\DTO\Aml;

use Apd\Trenergy\DTO\BaseDTO;

class AmlWalletTypeDTO extends BaseDTO
{
    public string $type;
    public array $available_coins;
}
