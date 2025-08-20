<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Partners;

use Apd\Trenergy\DTO\BaseDTO;

class StructureDTO extends BaseDTO
{
    public int $line;
    public array $users;

    protected array $nestedDtos = [
        'users' => PartnerDTO::class
    ];
}
