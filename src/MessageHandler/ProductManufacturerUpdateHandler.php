<?php

namespace App\MessageHandler;

use App\Message\ProductManufacturerUpdateNotification;
use App\Service\CatalogService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductManufacturerUpdateHandler
{

    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function __invoke(ProductManufacturerUpdateNotification $message)
    {

        $this->catalogService->addOrUpdateMultipleProductManufacturers($message->getItems());

    }

}
