<?php
declare(strict_types=1);

namespace Apd\Trenergy\DTO\Aml;

use Apd\Trenergy\DTO\BaseDTO;

class AmlListDTO extends BaseDTO
{
    public int $id;
    public string $address;
    public ?string $blockchain;
    public ?string $txid;
    public string $status;
    public AmlListContextDTO $context;
    public string $created_at;

    protected array $nestedDtos = [
        'context' => AmlListContextDTO::class
    ];
}
