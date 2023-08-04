<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductImageRepository;
use App\Repository\ProductManufacturerRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\UserRepository;
use App\Repository\WeborderRepository;

class StatisticsService {

    public function __construct(
        private ProductRepository $productRepo,
        private ProductCategoryRepository $productCategoryRepository,
        private ProductTypeRepository $productTypeRepository,
        private ProductManufacturerRepository $productManufacturerRepository,
        private ProductImageRepository $productImageRepository,
        private CustomerRepository $customerRepository,
        private WeborderRepository $weborderRepository,
        private UserRepository $userRepository
    ) {}

    public function getGeneralStatistics() {

        $productCount = $this->productRepo->count([]);
        $manufacturerCount = $this->productManufacturerRepository->count([]);
        $typeCount = $this->productTypeRepository->count([]);
        $categoryCount = $this->productCategoryRepository->count([]);
        $userCount = $this->userRepository->count([]);
        $weborderCount = $this->weborderRepository->count([]);
        $customerCount = $this->customerRepository->count([]);
        $productImageCount = $this->productImageRepository->count([]);

        return [
            'product_count' => $productCount,
            'manufacturer_count' => $manufacturerCount,
            'type_count' => $typeCount,
            'category_count' => $categoryCount,
            'user_count' => $userCount,
            'weborder_count' => $weborderCount,
            'customer_count' => $customerCount,
            'product_image_count' => $productImageCount
        ];

    }

}