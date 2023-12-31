<?php

namespace App\MessageHandler;

use App\Message\ProductUpdateNotification;
use App\Service\CatalogService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductUpdateHandler
{

    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function __invoke(ProductUpdateNotification $message)
    {

        $this->catalogService->addOrUpdateMultipleProducts($message->getItems());

    }

}
