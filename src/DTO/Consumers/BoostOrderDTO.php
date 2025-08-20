<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Consumers;

use Apd\Trenergy\DTO\BaseDTO;

class BoostOrderDTO extends BaseDTO
{
    public readonly int $status;
    public readonly int $completion_percentage;
    public readonly string $created_at;
    public readonly string $updated_at;
    public readonly string $valid_until;
}
