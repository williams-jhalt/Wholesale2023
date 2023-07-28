<?php

namespace App\Model;

use DateTime;

class Product {

    private ?string $itemNumber;
    private ?string $name;
    private ?string $typeCode;
    private ?string $manufacturerCode;
    private array $categoryCodes = [];
    private ?DateTime $releaseDate = null;

    public function getItemNumber(): ?string {
        return $this->itemNumber;
    }

    public function setItemNumber(string $itemNumber): void {
        $this->itemNumber = $itemNumber;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getTypeCode(): ?string {
        return $this->typeCode;
    }

    public function setTypeCode(string $typeCode): void {
        $this->typeCode = $typeCode;
    }

    public function getManufacturerCode(): ?string {
        return $this->manufacturerCode;
    }

    public function setManufacturerCode(string $manufacturerCode): void {
        $this->manufacturerCode = $manufacturerCode;
    }

    public function getCategoryCodes(): array {
        return $this->categoryCodes; 
    }

    public function setCategoryCodes(array $categoryCodes): void {
        $this->categoryCodes = $categoryCodes;
    }

    public function getReleaseDate(): ?DateTime {
        return $this->releaseDate;
    }

    public function setReleaseDate(DateTime $releaseDate): void {
        $this->releaseDate = $releaseDate;
    }

}