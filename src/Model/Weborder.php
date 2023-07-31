<?php

namespace App\Model;

class Weborder {

    private ?string $reference1;
    private ?string $reference2;
    private ?string $reference3;
    private ?string $reference4;
    private ?string $shipToName;
    private ?string $shipToAddress;
    private ?string $shipToAddress2;
    private ?string $shipToAddress3;
    private ?string $shipToCity;
    private ?string $shipToState;
    private ?string $shipToZip;
    private ?string $shipToCountry;
    private array $weborderItems;

    public function __construct() {
        $this->shipToCountry = "US";        
        $this->weborderItems = [];
    }

    public function getReference1(): ?string
    {
        return $this->reference1;
    }

    public function setReference1(string $reference1): void
    {
        $this->reference1 = $reference1;
    }

    public function getReference2(): ?string
    {
        return $this->reference2;
    }

    public function setReference2(string $reference2): void
    {
        $this->reference2 = $reference2;
    }

    public function getReference3(): ?string
    {
        return $this->reference3;
    }

    public function setReference3(string $reference3): void
    {
        $this->reference3 = $reference3;
    }

    public function getReference4(): ?string
    {
        return $this->reference4;
    }

    public function setReference4(string $reference4): void
    {
        $this->reference4 = $reference4;
    }

    public function getShipToName(): ?string
    {
        return $this->shipToName;
    }

    public function setShipToName(string $shipToName): void
    {
        $this->shipToName = $shipToName;        
    }

    public function getShipToAddress(): ?string
    {
        return $this->shipToAddress;
    }

    public function setShipToAddress(string $shipToAddress): void
    {
        $this->shipToAddress = $shipToAddress;
    }
    
    public function getShipToAddress2(): ?string
    {
        return $this->shipToAddress2;
    }

    public function setShipToAddress2(string $shipToAddress2): void
    {
        $this->shipToAddress2 = $shipToAddress2;        
    }

    public function getShipToAddress3(): ?string
    {
        return $this->shipToAddress3;
    }

    public function setShipToAddress3(string $shipToAddress3): void
    {
        $this->shipToAddress3 = $shipToAddress3;
    }

    public function getShipToCity(): ?string
    {
        return $this->shipToCity;        
    }

    public function setShipToCity(string $shipToCity): void
    {
        $this->shipToCity = $shipToCity;
    }

    public function getShipToState(): ?string
    {
        return $this->shipToState;
    }

    public function setShipToState(string $shipToState): void
    {
        $this->shipToState = $shipToState;
    }

    public function getShipToZip(): ?string
    {
        return $this->shipToZip;
    }

    public function setShipToZip(string $shipToZip): void
    {
        $this->shipToZip = $shipToZip;
    }

    public function getShipToCountry(): ?string
    {
        return $this->shipToCountry;
    }

    public function setShipToCountry(string $shipToCountry): void
    {
        $this->shipToCountry = $shipToCountry;
    }

    public function getWeborderItems(): array
    {
        return $this->weborderItems;
    }

    public function setWeborderItems(array $weborderItems): void
    {
        $this->weborderItems = $weborderItems;
    }

}