<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;

class BlockchainEnergyDTO extends BaseDTO
{
    public readonly float $energy_free;
    public readonly float $energy_total;
}
