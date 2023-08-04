<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\CatalogService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CatalogServiceTest extends KernelTestCase
{
    public function testUpdateFromErp(): void
    {
        $kernel = self::bootKernel();

        $container = static::getContainer();
        $service = $container->get(CatalogService::class);

        $product = new Product();
        $product->setItemNumber("AC1030812");

        $service->updateFromErp($product);

        $this->assertSame('test', $kernel->getEnvironment());
    }
}
