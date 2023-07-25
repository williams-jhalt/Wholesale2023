<?php

namespace App\Message;

class ProductManufacturerUpdateNotification
{

    public function __construct(
        private array $items
    ) {}

    public function getItems(): array
    {
        return $this->items;
    }

}