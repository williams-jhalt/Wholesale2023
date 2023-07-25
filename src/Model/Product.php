<?php

namespace App\Model;

use stdClass;

class Product extends stdClass {

    public string $itemNumber;
    public ?string $name;
    public ?string $typeCode;
    public ?string $manufacturerCode;
    public ?array $categoryCodes;
    public ?array $imageUrls;    
    public ?\DateTime $releaseDate;

    public function __construct() {
        $this->releaseDate = null;
    }

}