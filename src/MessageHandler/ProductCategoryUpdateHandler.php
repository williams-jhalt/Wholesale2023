<?php

namespace App\MessageHandler;

use App\Message\ProductCategoryUpdateNotification;
use App\Service\CatalogService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductCategoryUpdateHandler
{

    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function __invoke(ProductCategoryUpdateNotification $message)
    {

        $this->catalogService->addOrUpdateMultipleProductCategories($message->getItems());

    }

}
