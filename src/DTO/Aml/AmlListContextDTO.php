<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Aml;

use Apd\Trenergy\DTO\BaseDTO;

class AmlListContextDTO extends BaseDTO
{
    public bool $pending;
    /** @var EntityDTO[] */
    public array $entities;
    public float $riskScore;
}
