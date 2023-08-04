<?php

namespace App\MessageHandler;

use App\Message\UpdateProductFromErpMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UpdateProductFromErpMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private HttpClientInterface $httpClientInterface,
        private EntityManagerInterface $em,
        private string $erpConnectorUrl,
        private string $erpConnectorToken
    ) {
    }

    public function __invoke(UpdateProductFromErpMessage $message)
    {

        $itemNumber = $message->getItemNumber();

        $key = md5("erp_update:" . $itemNumber);

        $this->cache->get($key, function (ItemInterface $item) use ($itemNumber) {

            $this->logger->info("Fetching item " . $itemNumber . " from ERP");

            $item->expiresAfter(3600);

            $response = $this->httpClientInterface->request(
                'GET',
                $this->erpConnectorUrl . "/api/WTC/product/" . $itemNumber,
                [
                    'headers' => [
                        'X-AUTH-TOKEN' => $this->erpConnectorToken
                    ]
                ]
            );

            if ($response->getStatusCode() == 200) {

                $this->logger->info("Item Found!");

                $productData = json_decode($response->getContent());

                if ($productData !== null) {
                    $conn = $this->em->getConnection();

                    $sql = '
                UPDATE product p 
                SET p.release_date = :release_date, 
                p.price = :price, 
                p.stock_quantity = :stock_quantity 
                WHERE p.item_number = :item_number
                ';

                    $conn->executeStatement($sql, [
                        'release_date' => $productData->releaseDate,
                        'price' => $productData->wholesalePrice,
                        'stock_quantity' => $productData->quantityAvailable,
                        'item_number' => $itemNumber
                    ]);

                }

            }

        });

    }
}