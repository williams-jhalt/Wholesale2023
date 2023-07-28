<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\ProductManufacturer;
use App\Entity\ProductType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductManufacturerRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

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

    public function addOrUpdateMultipleProductCategories(array $categories): void {

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

    public function addOrUpdateProductCategory(\App\Model\ProductCategory $category): ProductCategory {

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

    public function addOrUpdateMultipleProductTypes(array $types): void {

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

    public function addOrUpdateProductType(\App\Model\ProductType $type): ProductType {

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

    public function addOrUpdateMultipleProductManufacturers(array $manufacturers): void {

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

    public function addOrUpdateProductManufacturer(\App\Model\ProductManufacturer $manufacturer): ProductManufacturer {

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

            $product = $this->productRepository->findOneByItemNumber($productData->getItemNumber());

            if ($product == null) {
                $product = new Product();
            }
    
            $product->setItemNumber($productData->getItemNumber());
            $product->setName($productData->getName());
            $product->setReleaseDate($productData->getReleaseDate());
    
            $productType = $this->productTypeRepository->findOneByCode($productData->getTypeCode());
    
            if ($productType == null) {
                $productType = new ProductType();
                $productType->setCode($productData->getTypeCode());
                $productType->setName($productData->getTypeCode());
                $this->entityManagerInterface->persist($productType);
                $this->entityManagerInterface->flush();
            }
    
            $product->setType($productType);
    
            $productManufacturer = $this->productManufacturerRepository->findOneByCode($productData->getManufacturerCode());
    
            if ($productManufacturer == null) {
                $productManufacturer = new ProductManufacturer();
                $productManufacturer->setCode($productData->getManufacturerCode());
                $productManufacturer->setName($productData->getManufacturerCode());
                $this->entityManagerInterface->persist($productManufacturer);
                $this->entityManagerInterface->flush();
            }
    
            $product->setManufacturer($productManufacturer);
    
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
    
            $this->entityManagerInterface->persist($product);

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

        $product = $this->productRepository->findOneByItemNumber($productData->getItemNumber());

        if ($product == null) {
            $product = new Product();
        }

        $product->setItemNumber($productData->getItemNumber());
        $product->setName($productData->getName());
        $product->setReleaseDate($productData->getReleaseDate());

        $productType = $this->productTypeRepository->findOneByCode($productData->getTypeCode());

        if ($productType == null) {
            $productType = new ProductType();
            $productType->setCode($productData->getTypeCode());
            $productType->setName($productData->getTypeCode());
            $this->entityManagerInterface->persist($productType);
        }

        $product->setType($productType);

        $productManufacturer = $this->productManufacturerRepository->findOneByCode($productData->getManufacturerCode());

        if ($productManufacturer == null) {
            $productManufacturer = new ProductManufacturer();
            $productManufacturer->setCode($productData->getManufacturerCode());
            $productManufacturer->setName($productData->getManufacturerCode());
            $this->entityManagerInterface->persist($productManufacturer);
        }

        $product->setManufacturer($productManufacturer);

        foreach ($productData->getCategoryCodes() as $code) {
            $category = $this->productCategoryRepository->findOneByCode($code);
            if ($category == null) {
                $category = new ProductCategory();
                $category->setCode($code);
                $category->setName($code);
                $this->entityManagerInterface->persist($category);
            }
            $product->addCategory($category);
        }

        $this->entityManagerInterface->persist($product);
        $this->entityManagerInterface->flush();

        return $product;
    }
}
