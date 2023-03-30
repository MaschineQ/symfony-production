<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Production;
use App\Entity\Receipt;
use App\Messaging\Notification\NotificationMessage;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationSubscriber implements EventSubscriber
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logActivity($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logActivity($args);
    }

    private function logActivity(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $message = false;

        if ($entity instanceof Production) {
            if (null !== $entity->getItems()) {
                foreach ($entity->getItems() as $items) {
                    if (null !== $items->getProduct()) {
                        foreach ($items->getProduct()->getPackagingMaterial() as $packagingnMaterial) {
                            if ($packagingnMaterial->getQuantity() <= NotificationMessage::PACKAGING_MATERIAL_CRITICAL_STOCK) {
                                $message = true;
                            }
                        }
                    }
                }
            }
        }

        if ($entity instanceof Receipt) {
            if (null !== $entity->getItems()) {
                foreach ($entity->getItems() as $items) {
                    if (null !== $items->getPackagingMaterial() && $items->getPackagingMaterial()->getQuantity() <= NotificationMessage::PACKAGING_MATERIAL_CRITICAL_STOCK) {
                        $message = true;
                    }
                }
            }
        }

        if ($message) {
            $this->bus->dispatch(new NotificationMessage($entity));
        }
    }
}
