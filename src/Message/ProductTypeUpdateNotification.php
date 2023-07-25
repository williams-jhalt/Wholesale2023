<?php

namespace App\Message;

class ProductTypeUpdateNotification
{

    public function __construct(
        private array $items
    ) {}

    public function getItems(): array
    {
        return $this->items;
    }

}