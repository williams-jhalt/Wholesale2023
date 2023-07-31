<?php

namespace App\Service;

use App\Repository\WeborderItemRepository;
use App\Repository\WeborderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Weborder as ImportWeborder;
use App\Entity\Weborder;
use App\Entity\WeborderItem;

class WeborderService 
{

    public function __construct(
        private WeborderRepository $weborderRepository,
        private WeborderItemRepository $weborderItemRepository,
        private EntityManagerInterface $entityManagerInterface
    ) {

    }

    public function createWeborder(ImportWeborder $importWeborder): string {

        $weborder = new Weborder(); 
        $weborder->setReference1($importWeborder->getReference1());
        $weborder->setReference2($importWeborder->getReference2());
        $weborder->setReference3($importWeborder->getReference3());
        $weborder->setShipToName($importWeborder->getShipToName());
        $weborder->setShipToAddress($importWeborder->getShipToAddress());
        $weborder->setShipToAddress2($importWeborder->getShipToAddress2());
        $weborder->setShipToAddress3($importWeborder->getShipToAddress3());
        $weborder->setShipToCity($importWeborder->getShipToCity());
        $weborder->setShipToState($importWeborder->getShipToState());
        $weborder->setShipToZip($importWeborder->getShipToZip());
        $weborder->setShipToCountry($importWeborder->getShipToCountry());

        foreach ($importWeborder->getWeborderItems() as $item) {
            $weborderItem = new WeborderItem();
            $weborderItem->setItemNumber($item->getItemNumber());
            $weborderItem->setQuantity($item->getQuantity());
            $weborder->addItem($weborderItem);
        }        

        $this->entityManagerInterface->persist($weborder);
        $this->entityManagerInterface->flush();

        return $weborder->getOrderNumber();

    }

}