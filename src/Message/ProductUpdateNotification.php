<?php

namespace App\Message;

class ProductUpdateNotification
{

    public function __construct(
        private array $products,
        private string $batch,
        private string $importKey
    ) {}

    public function getBatch(): string
    {
        return $this->batch;
    }

    public function getImportKey(): string
    {
        return $this->importKey;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

}