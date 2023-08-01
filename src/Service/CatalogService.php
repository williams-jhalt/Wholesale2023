<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\ProductImage;
use App\Entity\ProductManufacturer;
use App\Entity\ProductType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductManufacturerRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CatalogService
{

    public function __construct(
        private ProductRepository $productRepository,
        private ProductTypeRepository $productTypeRepository,
        private ProductManufacturerRepository $productManufacturerRepository,
        private ProductCategoryRepository $productCategoryRepository,
        private EntityManagerInterface $entityManagerInterface,
        private LoggerInterface $logger
    ) {
    }

    public function addOrUpdateMultipleProductCategories(array $categories): void
    {

        foreach ($categories as $category) {

            $m = $this->productCategoryRepository->findOneByCode($category->getCode());

            if ($m == null) {
                $m = new ProductCategory();
            }

            $m->setCode($category->getCode());
            $m->setName($category->getName());
            $this->entityManagerInterface->persist($m);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    public function addOrUpdateProductCategory(\App\Model\ProductCategory $category): ProductCategory
    {

        $m = $this->productCategoryRepository->findOneByCode($category->getCode());

        if ($m == null) {
            $m = new ProductCategory();
        }

        $m->setCode($category->getCode());
        $m->setName($category->getName());
        $this->entityManagerInterface->persist($m);
        $this->entityManagerInterface->flush();

        return $m;
    }

    public function addOrUpdateMultipleProductTypes(array $types): void
    {

        foreach ($types as $type) {

            $m = $this->productTypeRepository->findOneByCode($type->getCode());

            if ($m == null) {
                $m = new ProductType();
            }

            $m->setCode($type->getCode());
            $m->setName($type->getName());
            $this->entityManagerInterface->persist($m);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    public function addOrUpdateProductType(\App\Model\ProductType $type): ProductType
    {

        $m = $this->productTypeRepository->findOneByCode($type->getCode());

        if ($m == null) {
            $m = new ProductType();
        }

        $m->setCode($type->getCode());
        $m->setName($type->getName());
        $this->entityManagerInterface->persist($m);
        $this->entityManagerInterface->flush();

        return $m;
    }

    public function addOrUpdateMultipleProductManufacturers(array $manufacturers): void
    {

        foreach ($manufacturers as $manufacturer) {

            $m = $this->productManufacturerRepository->findOneByCode($manufacturer->getCode());

            if ($m == null) {
                $m = new ProductManufacturer();
            }

            $m->setCode($manufacturer->getCode());
            $m->setName($manufacturer->getName());
            $this->entityManagerInterface->persist($m);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    public function addOrUpdateProductManufacturer(\App\Model\ProductManufacturer $manufacturer): ProductManufacturer
    {

        $m = $this->productManufacturerRepository->findOneByCode($manufacturer->getCode());

        if ($m == null) {
            $m = new ProductManufacturer();
        }

        $m->setCode($manufacturer->getCode());
        $m->setName($manufacturer->getName());
        $this->entityManagerInterface->persist($m);
        $this->entityManagerInterface->flush();

        return $m;
    }

    public function addOrUpdateMultipleProducts(array $products): void
    {
        foreach ($products as $productData) {

            $this->processProduct($productData);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    /**
     * @param $productData
     * @return Product
     */
    public function addOrUpdateProduct(\App\Model\Product $productData): Product
    {

        $product = $this->processProduct($productData);

        $this->entityManagerInterface->flush();

        return $product;
    }

    private function processProduct(\App\Model\Product $productData): Product
    {

        $product = $this->productRepository->findOneByItemNumber($productData->getItemNumber());

        if ($product == null) {
            $product = new Product();
            $product->setItemNumber($productData->getItemNumber());
        }

        if ($productData->getName() !== null) {
            $product->setName($productData->getName());
        }

        if ($productData->getReleaseDate() !== null) {
            $product->setReleaseDate($productData->getReleaseDate());
        }

        if ($productData->getDescription() !== null) {
            $product->setDescription($productData->getDescription());
        }

        if ($productData->getKeywords() !== null) {
            $product->setKeywords($productData->getKeywords());
        }

        if ($productData->getPrice() !== null) {
            $product->setPrice($productData->getPrice());
        }

        if ($productData->getActive() !== null) {
            $product->setActive($productData->getActive());
        }

        if ($productData->getBarcode() !== null) {
            $product->setBarcode($productData->getBarcode());
        }

        if ($productData->getStockQuantity() !== null) {
            $product->setStockQuantity($productData->getStockQuantity());
        }

        if ($productData->getReorderQuantity() !== null) {
            $product->setReorderQuantity($productData->getReorderQuantity());
        }

        if ($productData->getVideo() !== null) {
            $product->setVideo($productData->getVideo());
        }

        if ($productData->getOnSale() !== null) {
            $product->setOnSale($productData->getOnSale());
        }

        if ($productData->getHeight() !== null) {
            $product->setHeight($productData->getHeight());
        }

        if ($productData->getLength() !== null) {
            $product->setLength($productData->getLength());
        }

        if ($productData->getWidth() !== null) {
            $product->setWidth($productData->getWidth());
        }

        if ($productData->getDiameter() !== null) {
            $product->setDiameter($productData->getDiameter());
        }

        if ($productData->getWeight() !== null) {
            $product->setWeight($productData->getWeight());
        }

        if ($productData->getColor() !== null) {
            $product->setColor($productData->getColor());
        }

        if ($productData->getMaterial() !== null) {
            $product->setMaterial($productData->getMaterial());
        }

        if ($productData->getDiscountable() !== null) {
            $product->setDiscountable($productData->getDiscountable());
        }

        if ($productData->getMaxDiscountRate() !== null) {
            $product->setMaxDiscountRate($productData->getMaxDiscountRate());
        }

        if ($productData->getSaleable() !== null) {
            $product->setSaleable($productData->getSaleable());
        }

        if ($productData->getInsertableLength() !== null) {
            $product->setProductLength($productData->getInsertableLength());
        }

        if ($productData->getRealistic() !== null) {
            $product->setRealistic($productData->getRealistic());
        }

        if ($productData->getBalls() !== null) {
            $product->setBalls($productData->getBalls());
        }

        if ($productData->getSuctionCup() !== null) {
            $product->setSuctionCup($productData->getSuctionCup());
        }

        if ($productData->getHarness() !== null) {
            $product->setHarness($productData->getHarness());
        }

        if ($productData->getVibrating() !== null) {
            $product->setVibrating($productData->getVibrating());
        }

        if ($productData->getThick() !== null) {
            $product->setThick($productData->getThick());
        }

        if ($productData->getDoubleEnded() !== null) {
            $product->setDoubleEnded($productData->getDoubleEnded());
        }

        if ($productData->getCircumference() !== null) {
            $product->setCircumference($productData->getCircumference());
        }

        if ($productData->getBrand() !== null) {
            $product->setBrand($productData->getBrand());
        }

        if ($productData->getMapPrice() !== null) {
            $product->setMapPrice($productData->getMapPrice());
        }

        if ($productData->getAmazonRestricted() !== null) {
            $product->setAmazonRestricted($productData->getAmazonRestricted());
        }

        if ($productData->getApprovalRequired() !== null) {
            $product->setApprovalRequired($productData->getApprovalRequired());
        }

        if ($productData->getTypeCode() !== null) {
            $productType = $this->productTypeRepository->findOneByCode($productData->getTypeCode());

            if ($productType == null) {
                $productType = new ProductType();
                $productType->setCode($productData->getTypeCode());
                $productType->setName($productData->getTypeCode());
                $this->entityManagerInterface->persist($productType);
                $this->entityManagerInterface->flush();
            }

            $product->setType($productType);
        }


        if ($productData->getManufacturerCode() !== null) {
            $productManufacturer = $this->productManufacturerRepository->findOneByCode($productData->getManufacturerCode());

            if ($productManufacturer == null) {
                $productManufacturer = new ProductManufacturer();
                $productManufacturer->setCode($productData->getManufacturerCode());
                $productManufacturer->setName($productData->getManufacturerCode());
                $this->entityManagerInterface->persist($productManufacturer);
                $this->entityManagerInterface->flush();
            }

            $product->setManufacturer($productManufacturer);
        }

        if (!empty($productData->getCategoryCodes())) {

            foreach ($productData->getCategoryCodes() as $code) {
                $category = $this->productCategoryRepository->findOneByCode($code);
                if ($category == null) {
                    $category = new ProductCategory();
                    $category->setCode($code);
                    $category->setName($code);
                    $this->entityManagerInterface->persist($category);
                    $this->entityManagerInterface->flush();
                }
                $product->addCategory($category);
            }
        }

        $this->entityManagerInterface->persist($product);
        $this->entityManagerInterface->flush();         

        foreach ($productData->getImages() as $imageData) {    
                      
            $image = new ProductImage();
            if ($imageData->getFile() !== null) {
                $image->setImageFile($imageData->getFile());
                $image->setProduct($product);
                $this->entityManagerInterface->persist($image);
                $this->entityManagerInterface->flush();         
            } elseif ($imageData->getImageUrl() !== null) { 
                $fh = tempnam(sys_get_temp_dir(), "image_import");
                if (false !== file_put_contents($fh, file_get_contents($imageData->getImageUrl()))) {
                    $image->setImageFile(new UploadedFile($fh, $imageData->getOriginalFilename(), null, null, true));       
                    $image->setProduct($product);
                    $this->entityManagerInterface->persist($image);
                    $this->entityManagerInterface->flush();         
                }
            }
        }

        return $product;
    }
}
