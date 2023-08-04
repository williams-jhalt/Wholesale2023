<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\ProductImage;
use App\Entity\ProductManufacturer;
use App\Entity\ProductType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductImageRepository;
use App\Repository\ProductManufacturerRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CatalogService
{

    public function __construct(
        private ProductRepository $productRepository,
        private ProductTypeRepository $productTypeRepository,
        private ProductManufacturerRepository $productManufacturerRepository,
        private ProductCategoryRepository $productCategoryRepository,
        private ProductImageRepository $productImageRepository,
        private EntityManagerInterface $entityManagerInterface,
        private HttpClientInterface $httpClientInterface,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private string $erpConnectorUrl,
        private string $erpConnectorToken,
        private string $importDir
    ) {
    }

    public function addOrUpdateMultipleProductCategories(array $categories): void
    {

        foreach ($categories as $category) {
            $this->processCategory($category);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    public function addOrUpdateProductCategory(\App\Model\ProductCategory $category): ProductCategory
    {
        $m = $this->processCategory($category);
        $this->entityManagerInterface->flush();

        return $m;
    }

    public function addOrUpdateMultipleProductTypes(array $types): void
    {

        foreach ($types as $type) {
            $this->processType($type);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    public function addOrUpdateProductType(\App\Model\ProductType $type): ProductType
    {

        $m = $this->processType($type);
        $this->entityManagerInterface->flush();

        return $m;
    }

    public function addOrUpdateMultipleProductManufacturers(array $manufacturers): void
    {

        foreach ($manufacturers as $manufacturer) {
            $this->processManufacturer($manufacturer);
        }

        $this->entityManagerInterface->flush();
        $this->entityManagerInterface->clear();
    }

    public function addOrUpdateProductManufacturer(\App\Model\ProductManufacturer $manufacturer): ProductManufacturer
    {

        $m = $this->processManufacturer($manufacturer);
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

    private function processCategory(\App\Model\ProductCategory $productCategoryData): ProductCategory
    {

        $m = $this->productCategoryRepository->findOneByCode($productCategoryData->getCode());

        if ($m == null) {
            $m = new ProductCategory();
        }

        $m->setCode($productCategoryData->getCode());
        $m->setName($productCategoryData->getName());
        $this->entityManagerInterface->persist($m);

        return $m;

    }

    private function processManufacturer(\App\Model\ProductManufacturer $productManufacturerData): ProductManufacturer
    {

        $m = $this->productManufacturerRepository->findOneByCode($productManufacturerData->getCode());

        if ($m == null) {
            $m = new ProductManufacturer();
        }

        $m->setCode($productManufacturerData->getCode());
        $m->setName($productManufacturerData->getName());
        $this->entityManagerInterface->persist($m);

        return $m;

    }

    private function processType(\App\Model\ProductType $productTypeData): ProductType
    {
        $m = $this->productTypeRepository->findOneByCode($productTypeData->getCode());

        if ($m == null) {
            $m = new ProductType();
        }

        $m->setCode($productTypeData->getCode());
        $m->setName($productTypeData->getName());
        $this->entityManagerInterface->persist($m);

        return $m;

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

        if (!empty($productData->getImages())) {

            $images = $this->productImageRepository->findBy(['product' => $product]);

            foreach ($productData->getImages() as $imageData) {

                $exists = false;
                foreach ($images as $i) {
                    if ($i->getImage()->getOriginalName() == $imageData->getOriginalFilename()) {
                        $this->logger->info("Image exists; skipping");
                        $exists = true;
                    }
                }

                if ($exists) {
                    continue;
                }

                $image = new ProductImage();

                $filesystem = new Filesystem();
                $filesystem->mkdir($this->importDir);

                $fh = tempnam($this->importDir, "image_import");

                $this->logger->info("Creating temporary file " . $fh);

                if (false !== file_put_contents($fh, file_get_contents($imageData->getImageUrl()))) {
                    $image->setImageFile(new UploadedFile($fh, $imageData->getOriginalFilename(), null, null, true));
                    $image->setProduct($product);
                    $this->entityManagerInterface->persist($image);
                    $this->entityManagerInterface->flush();

                    $this->logger->info("Added new image file " . $image->getImage()->getName());
                }

            }

        }

        return $product;
    }
}