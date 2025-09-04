<?php

namespace Apd\Trenergy\DTO\Aml;

use Apd\Trenergy\DTO\BaseDTO;

class AmlContextDTO extends BaseDTO
{
    public bool $pending;
    public array $entities;
    public float $riskScore;
}
