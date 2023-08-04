<?php

namespace App\Message;

final class UpdateProductFromErpMessage
{

    private $itemNumber;

    public function __construct(string $itemNumber)
    {
        $this->itemNumber = $itemNumber;
    }

    public function getItemNumber(): string
    {
        return $this->itemNumber;
    }

}
