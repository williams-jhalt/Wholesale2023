<?php

namespace App\Model;

class WeborderItem {

    private ?string $itemNumber;
    private int $quantity;

    public function __construct() {
        $this->quantity = 1;
    }

    public function getItemNumber(): ?string 
    {
        return $this->itemNumber;
    }

    public function setItemNumber(string $itemNumber): void
    {
        $this->itemNumber = $itemNumber;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }    

}