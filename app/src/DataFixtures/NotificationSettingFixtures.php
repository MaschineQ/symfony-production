<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\NotificationSetting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class NotificationSettingFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['test-data'];
    }

    public function load(ObjectManager $manager): void
    {
        $notificationSetting = new NotificationSetting();
        $notificationSetting->setId(1);
        $notificationSetting->setStockCritical(20);
        $notificationSetting->setStockLow(10);

        $manager->persist($notificationSetting);
        $manager->flush();
    }
}
