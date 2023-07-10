<?php

namespace App\Service;

use App\Repository\ProductCategoryRepository;
use App\Repository\ProductManufacturerRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;

class StatisticsService {

    public function __construct(
        private ProductRepository $productRepo,
        private ProductCategoryRepository $productCategoryRepository,
        private ProductTypeRepository $productTypeRepository,
        private ProductManufacturerRepository $productManufacturerRepository
    ) {}

    public function getGeneralStatistics() {

        $productCount = $this->productRepo->count([]);
        $manufacturerCount = $this->productManufacturerRepository->count([]);
        $typeCount = $this->productTypeRepository->count([]);
        $categoryCount = $this->productCategoryRepository->count([]);

        return [
            'product_count' => $productCount,
            'manufacturer_count' => $manufacturerCount,
            'type_count' => $typeCount,
            'category_count' => $categoryCount
        ];

    }

}