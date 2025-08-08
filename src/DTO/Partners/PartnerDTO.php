<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Partners;

use Apd\Trenergy\DTO\BaseDTO;

class PartnerDTO extends BaseDTO
{
        public int $id;
        public string $name;
        public string $photo;
        public int $leader_level;
        public string $level_name;
        public ?int $leader_id;
        public float $stake;
        public int $active_stakers_count;
        public int $total_stakes_in_structure;
        public int $total_active_stakers_in_structure;
        public int $total_partners_in_structure;
}
