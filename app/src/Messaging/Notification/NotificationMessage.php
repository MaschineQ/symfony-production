<?php

declare(strict_types=1);

namespace App\Messaging\Notification;

class NotificationMessage
{
    public const PACKAGING_MATERIAL_LOW_STOCK = '20';
    public const PACKAGING_MATERIAL_CRITICAL_STOCK = '10';

    public function __construct(
        private object $entity,
    ) {
    }

    public function getEntity(): object
    {
        return $this->entity;
    }
}
