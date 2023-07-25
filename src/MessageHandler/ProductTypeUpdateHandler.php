<?php

namespace App\MessageHandler;

use App\Message\ProductTypeUpdateNotification;
use App\Service\CatalogService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductTypeUpdateHandler
{

    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function __invoke(ProductTypeUpdateNotification $message)
    {

        $this->catalogService->addOrUpdateMultipleProductTypes($message->getItems());

    }

}
