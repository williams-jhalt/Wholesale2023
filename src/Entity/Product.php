<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'product:item']),
        new GetCollection(normalizationContext: ['groups' => 'product:list'])
    ],
    order: ['releaseDate' => 'DESC', 'itemNumber' => 'ASC'],
    paginationEnabled: true
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list', 'product:item'])]
    private ?string $itemNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list', 'product:item'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['product:item'])]
    private ?ProductType $type = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['product:item'])]
    private ?ProductManufacturer $manufacturer = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, orphanRemoval: true)]
    #[Groups(['product:item'])]
    private Collection $images;

    #[ORM\ManyToMany(targetEntity: ProductCategory::class, inversedBy: 'products')]
    #[Groups(['product:item'])]
    private Collection $categories;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['product:list', 'product:item'])]
    private ?\DateTimeInterface $releaseDate = null;

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
}
