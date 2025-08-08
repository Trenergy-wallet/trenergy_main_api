<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Aml;

use Apd\Trenergy\DTO\BaseDTO;

class EntityDTO extends BaseDTO
{
    public string $level;
    public string $entity;
    public float $riskScore;

    protected array $casts = [
        'riskScore' => 'float',
    ];
}
