<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Message\UpdateProductFromErpMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postLoad, priority: 500, connection: 'default')]
class ProductEventListener {

    /**
     * Summary of __construct
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Symfony\Component\Messenger\MessageBusInterface $bus
     */
    public function __construct(
        private LoggerInterface $logger,
        private MessageBusInterface $bus
        ) {}

    /**
     * Summary of postLoad
     * @param \Doctrine\Persistence\Event\LifecycleEventArgs $postLoad
     * @return void
     */
    public function postLoad(LifecycleEventArgs $postLoad): void
    {
        $entity = $postLoad->getObject();
        if ($entity instanceof Product) {
            $itemNumber = $entity->getItemNumber();
            $this->logger->info("Updating " . $itemNumber . " from ERP");
            $this->bus->dispatch(new UpdateProductFromErpMessage($itemNumber));
        }
    }

}