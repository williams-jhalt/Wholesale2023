<?php

namespace App\MessageHandler;

use App\Message\ProductUpdateNotification;
use App\Service\CatalogService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductUpdateHandler
{

    public function __construct(
        private ContainerBagInterface $params,
        private CatalogService $catalogService
    ) {}

    public function __invoke(ProductUpdateNotification $message)
    {

        $this->catalogService->addOrUpdateMultipleProducts($message->getProducts());
        $filesystem = new Filesystem();
        $filesystem->remove($this->params->get("app.import_dir") . "/product_import/" . $message->getImportKey() . "_" . $message->getBatch() . ".tmp");

    }

}
