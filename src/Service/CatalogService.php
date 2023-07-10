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

class CatalogService {

    public function __construct(
        private ProductRepository $productRepository,
        private ProductTypeRepository $productTypeRepository,
        private ProductManufacturerRepository $productManufacturerRepository,
        private ProductCategoryRepository $productCategoryRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {}

    /**
     * @param $productData
     * @return Product
     */
    public function addOrUpdateProduct(array $productData): Product {

        $product = $this->productRepository->findOneByItemNumber($productData['sku']);

        if ($product == null) {
            $product = new Product();
        }

        $product->setItemNumber($productData['sku']);
        $product->setName($productData['name']);
        $product->setReleaseDate($productData['releaseDate']);

        $productType = $this->productTypeRepository->findOneByCode($productData['type']);

        if ($productType == null) {
            $productType = new ProductType();
            $productType->setCode($productData['type']);
            $productType->setName($productData['type']);
            $this->entityManagerInterface->persist($productType);
        }

        $product->setType($productType);

        $productManufacturer = $this->productManufacturerRepository->findOneByCode($productData['manufacturer']);

        if ($productManufacturer == null) {
            $productManufacturer = new ProductManufacturer();
            $productManufacturer->setCode($productData['manufacturer']);
            $productManufacturer->setName($productData['manufacturer']);
            $this->entityManagerInterface->persist($productManufacturer);
        }

        $product->setManufacturer($productManufacturer);

        foreach ($productData['categories'] as $code) {
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