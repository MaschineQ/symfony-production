<?php

declare(strict_types=1);

namespace App\Messaging\Notification;

use App\Entity\NotificationSetting;
use App\Repository\PackagingMaterialRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationHandler
{
    public function __construct(
        private Mailer         $mailer,
        private UserRepository $users,
        private PackagingMaterialRepository $packagingMaterials,
        private EntityManagerInterface  $entityManager,
    ) {
    }

    public function __invoke(NotificationMessage $packagingMaterial): void
    {
        $packagingMaterials = $this->packagingMaterials->findBy([], ['quantity' => 'ASC']);

        $notificationSettings = $this->entityManager->getRepository(NotificationSetting::class)->find(
            1
        ) ?? throw new \Exception(
            'Notification settings not found'
        );

        $this->mailer->send(
            $this->getEmails(),
            'Notification',
            'notifications/notification_mail.html.twig',
            [
                'packagingMaterials' => $packagingMaterials,
                'packagingMaterialCritical' => $notificationSettings->getStockCritical(),
                'packagingMaterialLow' => $notificationSettings->getStockLow(),
            ]
        );
    }

    // todo zasilani notifikaci dle nastaveni z db
    public function getEmails(): string
    {
        $users = $this->users->findAll();

        $emails = [];
        foreach ($users as $user) {
            $emails[] = $user->getEmail();
        }

        return implode(',', $emails);
    }
}
