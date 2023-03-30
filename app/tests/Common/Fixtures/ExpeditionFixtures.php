<?php

declare(strict_types=1);

namespace App\Tests\Common\Fixtures;

use App\DataFixtures\NotificationSettingFixtures;
use App\Entity\Expedition;
use App\Entity\ExpeditionItem;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExpeditionFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [ProductFixtures::class, NotificationSettingFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['test-data'];
    }

    public function load(ObjectManager $manager): void
    {
        $product = $manager->getRepository(Product::class);

        $expedition = new Expedition();
        $expedition->setExpeditionDate(new \DateTimeImmutable());

        $expeditionItem = new ExpeditionItem();
        $expeditionItem->setProduct($product->findOneByName('Tee') ?? throw new \Exception('Product not found'));
        $expeditionItem->setQuantity(10);

        $expedition->addItem($expeditionItem);

        $manager->persist($expedition);

        $manager->flush();
    }
}
