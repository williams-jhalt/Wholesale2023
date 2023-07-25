<?php

namespace App\Message;

class ProductUpdateNotification
{

    public function __construct(
        private array $items
    ) {}

    public function getItems(): array
    {
        return $this->items;
    }

}