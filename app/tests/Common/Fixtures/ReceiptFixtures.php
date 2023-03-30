<?php

declare(strict_types=1);

namespace App\Tests\Common\Fixtures;

use App\DataFixtures\NotificationSettingFixtures;
use App\DataFixtures\PackagingMaterialFixtures;
use App\Entity\PackagingMaterial;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [PackagingMaterialFixtures::class, NotificationSettingFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['test-data'];
    }

    public function load(ObjectManager $manager): void
    {
        $packagingMaterial = $manager->getRepository(PackagingMaterial::class);

        $receipt = new Receipt();
        $receipt->setReceiptDate(new \DateTimeImmutable());

        $receiptItem = new ReceiptItem();
        $receiptItem->setPackagingMaterial(
            $packagingMaterial->findOneByName('Packaging 1') ?? throw new \Exception('Product not found')
        );
        $receiptItem->setQuantity(10);

        $receipt->addItem($receiptItem);

        $manager->persist($receipt);

        $manager->flush();
    }
}
