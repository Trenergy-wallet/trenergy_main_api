<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Stakes;

use Apd\Trenergy\DTO\BaseDTO;

class GetStakeDTO extends BaseDTO
{
        public int $id;
        public float $trx_amount;
        public int $type;
        public bool $is_closes;
        public ?string $closes_at;
        public ?string $created_at;
        public ?string  $available_at;
        public ?string $next_reward_at;
}
