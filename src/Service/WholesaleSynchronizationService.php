<?php

namespace App\Service;

use App\Message\ProductUpdateNotification;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WholesaleSynchronizationService {

    public function __construct(
        private CatalogService $catalogService,
        private HttpClientInterface $client,
        private MessageBusInterface $bus,
        private string $wholesaleRestUrl
    ) {}

    public function sync() {

        $start = 0;
        $count = 10;

        do {

            $response = $this->client->request(
                'GET',
                $this->wholesaleRestUrl + "/products",
                [
                    'query' => [
                        'format' => 'json',
                        'active' => 1,
                        'start' => $start,
                        'count' => $count
                    ]
                ]
            );

            $data = $response->toArray();

            $products = [];

            foreach ($data['products'] as $p) {

                $product = new \App\Model\Product();
                $product->setItemNumber($p['sku'])
                ->setName($p['name'])
                ->setDescription($p['description'])
                ->setKeywords($p['keywords'])
                ->setPrice($p['price'])
                ->setActive($p['active'])
                ->setBarcode($p['barcode'])
                ->setStockQuantity($p['stock_quantity'])
                ->setReorderQuantity($p['reorder_quantity'])
                ->setVideo($p['video'])
                ->setOnSale($p['on_sale'])
                ->setHeight($p['height'])
                ->setLength($p['length'])
                ->setWidth($p['width'])
                ->setDiameter($p['diameter'])
                ->setWeight($p['weight'])
                ->setColor($p['color'])
                ->setMaterial($p['material'])
                ->setReleaseDate(\DateTimeImmutable::createFromFormat("Y-m-d", $p['release_date']))
                ->setDiscountable($p['discountable'])
                ->setMaxDiscountRate($p['max_discount_rate'])
                ->setSaleable($p['saleable'])
                ->setProductLength($p['product_length'])
                ->setInsertableLength($p['insertable_length'])
                ->setRealistic($p['realistic'])
                ->setBalls($p['balls'])
                ->setSuctionCup($p['suction_cup'])
                ->setHarness($p['harness'])
                ->setVibrating($p['vibrating'])
                ->setThick($p['thick'])
                ->setDoubleEnded($p['double_ended'])
                ->setCircumference($p['circumference'])
                ->setBrand($p['brand'])
                ->setMapPrice($p['map_price'])
                ->setAmazonRestricted($p['amazon_restricted'])
                ->setApprovalRequired($p['approval_required'])
                ->setTypeCode($p['type']['code'])
                ->setManufacturerCode($p['manufacturer']['code']);

                if (!empty($p['categories'])) {
                    $categories = [];
                    foreach ($p['categories'] as $c) {
                        $categories[] = $c['code'];
                    }
                    $product->setCategoryCodes($categories);
                }

                $images = [];
                foreach ($p['images'] as $i) {
                    $image = new \App\Model\ProductImage();
                    $image->setOriginalFilename($i['filename']);
                    $image->setImageUrl($i['image_url']);
                    $images[] = $image;
                }
                $product->setImages($images);

                $products[] = $product;

            }

            $this->bus->dispatch(new ProductUpdateNotification($products));

            $start = $start + $count;
 
        } while (sizeof($data['products']) > 0);

    }

}