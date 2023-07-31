<?php

namespace App\Entity;

use App\Repository\WeborderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeborderRepository::class)]
class Weborder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference3 = null;

    #[ORM\Column(length: 255)]
    private ?string $shipToName = null;

    #[ORM\Column(length: 255)]
    private ?string $shipToAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipToAddress2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipToAddress3 = null;

    #[ORM\Column(length: 255)]
    private ?string $shipToCity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipToState = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shipToZip = null;

    #[ORM\Column(length: 255)]
    private ?string $shipToCountry = null;

    #[ORM\OneToMany(mappedBy: 'weborder', targetEntity: WeborderItem::class, orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\ManyToOne(inversedBy: 'weborders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    public function __construct()
    {
        // create a unique order number
        $this->orderNumber = uniqid(date("Ymd"));
        $this->orderDate = new DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getReference1(): ?string
    {
        return $this->reference1;
    }

    public function setReference1(?string $reference1): static
    {
        $this->reference1 = $reference1;

        return $this;
    }

    public function getReference2(): ?string
    {
        return $this->reference2;
    }

    public function setReference2(?string $reference2): static
    {
        $this->reference2 = $reference2;

        return $this;
    }

    public function getReference3(): ?string
    {
        return $this->reference3;
    }

    public function setReference3(?string $reference3): static
    {
        $this->reference3 = $reference3;

        return $this;
    }

    public function getShipToName(): ?string
    {
        return $this->shipToName;
    }

    public function setShipToName(string $shipToName): static
    {
        $this->shipToName = $shipToName;

        return $this;
    }

    public function getShipToAddress(): ?string
    {
        return $this->shipToAddress;
    }

    public function setShipToAddress(string $shipToAddress): static
    {
        $this->shipToAddress = $shipToAddress;

        return $this;
    }

    public function getShipToAddress2(): ?string
    {
        return $this->shipToAddress2;
    }

    public function setShipToAddress2(?string $shipToAddress2): static
    {
        $this->shipToAddress2 = $shipToAddress2;

        return $this;
    }

    public function getShipToAddress3(): ?string
    {
        return $this->shipToAddress3;
    }

    public function setShipToAddress3(?string $shipToAddress3): static
    {
        $this->shipToAddress3 = $shipToAddress3;

        return $this;
    }

    public function getShipToCity(): ?string
    {
        return $this->shipToCity;
    }

    public function setShipToCity(string $shipToCity): static
    {
        $this->shipToCity = $shipToCity;

        return $this;
    }

    public function getShipToState(): ?string
    {
        return $this->shipToState;
    }

    public function setShipToState(?string $shipToState): static
    {
        $this->shipToState = $shipToState;

        return $this;
    }

    public function getShipToZip(): ?string
    {
        return $this->shipToZip;
    }

    public function setShipToZip(?string $shipToZip): static
    {
        $this->shipToZip = $shipToZip;

        return $this;
    }

    public function getShipToCountry(): ?string
    {
        return $this->shipToCountry;
    }

    public function setShipToCountry(string $shipToCountry): static
    {
        $this->shipToCountry = $shipToCountry;

        return $this;
    }

    /**
     * @return Collection<int, WeborderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(WeborderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setWeborder($this);
        }

        return $this;
    }

    public function removeItem(WeborderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getWeborder() === $this) {
                $item->setWeborder(null);
            }
        }

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
