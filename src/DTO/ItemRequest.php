<?php
declare(strict_types=1);

namespace App\DTO;

final readonly class ItemRequest
{
    public function __construct(
        /** @var Item[] */
        public array $items,

    ) {}
}