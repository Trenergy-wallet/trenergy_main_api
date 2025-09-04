<?php

namespace Apd\Trenergy\DTO\Aml;

use Apd\Trenergy\DTO\BaseDTO;

class AmlCheckResultDTO extends BaseDTO
{
    public int $id;
    public string $address;
    public ?string $blockchain;
    public ?string $txid;
    public string $status;
    public AmlContextDTO $context;
    public string $created_at;
    
    protected array $casts = [
        'id' => 'int',
    ];
    
    protected array $nestedDtos = [
        'context' => AmlContextDTO::class,
    ];
}