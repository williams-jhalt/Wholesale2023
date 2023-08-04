<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $itemNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?ProductType $type = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?ProductManufacturer $manufacturer = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\ManyToMany(targetEntity: ProductCategory::class, inversedBy: 'products')]
    private Collection $categories;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $keywords = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column]
    private ?bool $active = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $barcode = null;

    #[ORM\Column]
    private ?int $stockQuantity = 0;

    #[ORM\Column]
    private ?int $reorderQuantity = 0;

    #[ORM\Column]
    private ?bool $video = false;

    #[ORM\Column]
    private ?bool $onSale = false;

    #[ORM\Column(nullable: true)]
    private ?float $height = null;

    #[ORM\Column(nullable: true)]
    private ?float $length = null;

    #[ORM\Column(nullable: true)]
    private ?float $width = null;

    #[ORM\Column(nullable: true)]
    private ?float $diameter = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $material = null;

    #[ORM\Column]
    private ?bool $discountable = false;

    #[ORM\Column]
    private ?float $maxDiscountRate = 0;

    #[ORM\Column]
    private ?bool $saleable = false;

    #[ORM\Column(nullable: true)]
    private ?float $productLength = null;

    #[ORM\Column(nullable: true)]
    private ?float $insertableLength = null;

    #[ORM\Column]
    private ?bool $realistic = false;

    #[ORM\Column]
    private ?bool $balls = false;

    #[ORM\Column]
    private ?bool $suctionCup = false;

    #[ORM\Column]
    private ?bool $harness = false;

    #[ORM\Column]
    private ?bool $vibrating = false;

    #[ORM\Column]
    private ?bool $thick = false;

    #[ORM\Column]
    private ?bool $doubleEnded = false;

    #[ORM\Column(nullable: true)]
    private ?float $circumference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '2', nullable: true)]
    private ?string $mapPrice = null;

    #[ORM\Column]
    private ?bool $amazonRestricted = false;

    #[ORM\Column]
    private ?bool $approvalRequired = false;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemNumber(): ?string
    {
        return $this->itemNumber;
    }

    public function setItemNumber(string $itemNumber): static
    {
        $this->itemNumber = $itemNumber;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?ProductType
    {
        return $this->type;
    }

    public function setType(?ProductType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getManufacturer(): ?ProductManufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?ProductManufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ProductCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(ProductCategory $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): static
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): static
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    public function getReorderQuantity(): ?int
    {
        return $this->reorderQuantity;
    }

    public function setReorderQuantity(int $reorderQuantity): static
    {
        $this->reorderQuantity = $reorderQuantity;

        return $this;
    }

    public function isVideo(): ?bool
    {
        return $this->video;
    }

    public function setVideo(bool $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    public function setOnSale(bool $onSale): static
    {
        $this->onSale = $onSale;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getDiameter(): ?float
    {
        return $this->diameter;
    }

    public function setDiameter(?float $diameter): static
    {
        $this->diameter = $diameter;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function isDiscountable(): ?bool
    {
        return $this->discountable;
    }

    public function setDiscountable(bool $discountable): static
    {
        $this->discountable = $discountable;

        return $this;
    }

    public function getMaxDiscountRate(): ?float
    {
        return $this->maxDiscountRate;
    }

    public function setMaxDiscountRate(float $maxDiscountRate): static
    {
        $this->maxDiscountRate = $maxDiscountRate;

        return $this;
    }

    public function isSaleable(): ?bool
    {
        return $this->saleable;
    }

    public function setSaleable(bool $saleable): static
    {
        $this->saleable = $saleable;

        return $this;
    }

    public function getProductLength(): ?float
    {
        return $this->productLength;
    }

    public function setProductLength(?float $productLength): static
    {
        $this->productLength = $productLength;

        return $this;
    }

    public function getInsertableLength(): ?float
    {
        return $this->insertableLength;
    }

    public function setInsertableLength(?float $insertableLength): static
    {
        $this->insertableLength = $insertableLength;

        return $this;
    }

    public function isRealistic(): ?bool
    {
        return $this->realistic;
    }

    public function setRealistic(bool $realistic): static
    {
        $this->realistic = $realistic;

        return $this;
    }

    public function isBalls(): ?bool
    {
        return $this->balls;
    }

    public function setBalls(bool $balls): static
    {
        $this->balls = $balls;

        return $this;
    }

    public function isSuctionCup(): ?bool
    {
        return $this->suctionCup;
    }

    public function setSuctionCup(bool $suctionCup): static
    {
        $this->suctionCup = $suctionCup;

        return $this;
    }

    public function isHarness(): ?bool
    {
        return $this->harness;
    }

    public function setHarness(bool $harness): static
    {
        $this->harness = $harness;

        return $this;
    }

    public function isVibrating(): ?bool
    {
        return $this->vibrating;
    }

    public function setVibrating(bool $vibrating): static
    {
        $this->vibrating = $vibrating;

        return $this;
    }

    public function isThick(): ?bool
    {
        return $this->thick;
    }

    public function setThick(bool $thick): static
    {
        $this->thick = $thick;

        return $this;
    }

    public function isDoubleEnded(): ?bool
    {
        return $this->doubleEnded;
    }

    public function setDoubleEnded(bool $doubleEnded): static
    {
        $this->doubleEnded = $doubleEnded;

        return $this;
    }

    public function getCircumference(): ?float
    {
        return $this->circumference;
    }

    public function setCircumference(?float $circumference): static
    {
        $this->circumference = $circumference;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getMapPrice(): ?string
    {
        return $this->mapPrice;
    }

    public function setMapPrice(?string $mapPrice): static
    {
        $this->mapPrice = $mapPrice;

        return $this;
    }

    public function isAmazonRestricted(): ?bool
    {
        return $this->amazonRestricted;
    }

    public function setAmazonRestricted(bool $amazonRestricted): static
    {
        $this->amazonRestricted = $amazonRestricted;

        return $this;
    }

    public function isApprovalRequired(): ?bool
    {
        return $this->approvalRequired;
    }

    public function setApprovalRequired(bool $approvalRequired): static
    {
        $this->approvalRequired = $approvalRequired;

        return $this;
    }
}
