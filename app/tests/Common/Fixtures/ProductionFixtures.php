<?php

declare(strict_types=1);

namespace App\Tests\Common\Fixtures;

use App\DataFixtures\NotificationSettingFixtures;
use App\Entity\Product;
use App\Entity\Production;
use App\Entity\ProductionItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductionFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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

        $production = new Production();
        $production->setproductionDate(new \DateTimeImmutable());
        $production->setCreatedAt(new \DateTimeImmutable());

        $productionItem = new ProductionItem();
        $productionItem->setProduct($product->findOneByName('Tee') ?? throw new \Exception('Product not found'));
        $productionItem->setQuantity(10);

        $production->addItem($productionItem);

        $manager->persist($production);

        $manager->flush();
    }
}
