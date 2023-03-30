<?php

declare(strict_types=1);

namespace App\Tests\Common\Fixtures;

use App\DataFixtures\PackagingMaterialFixtures;
use App\Entity\Category;
use App\Entity\PackagingMaterial;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, PackagingMaterialFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['test-data'];
    }

    public function load(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product1->setName('Tee');
        $product1->setPackagingType('l');
        $product1->setQuantityPerPiece(10);
        $category1 = $manager->getRepository(Category::class);
        $product1->setCategory($category1->findOneByName('Drinks') ?? throw new \Exception('Category not found'));

        $packagingMaterials = $manager->getRepository(PackagingMaterial::class)->findByName(
            [
                PackagingMaterialFixtures::FIRST_PACKAGING_MATERIAL_PACKAGING,
                PackagingMaterialFixtures::FIRST_PACKAGING_MATERIAL_LABEL
            ]
        );

        foreach ($packagingMaterials as $packagingMaterial) {
            $product1->addPackagingMaterial($packagingMaterial);
        }
        $manager->persist($product1);


        $product2 = new Product();
        $product2->setName('Coffee');
        $product2->setPackagingType('l');
        $product2->setQuantityPerPiece(10);
        $category2 = $manager->getRepository(Category::class);
        $product2->setCategory($category2->findOneByName('Drinks') ?? throw new \Exception('Category not found'));

        $packagingMaterials = $manager->getRepository(PackagingMaterial::class)->findByName(
            [
                PackagingMaterialFixtures::SECOND_PACKAGING_MATERIAL_PACKAGING,
                PackagingMaterialFixtures::SECOND_PACKAGING_MATERIAL_LABEL
            ]
        );

        foreach ($packagingMaterials as $packagingMaterial) {
            $product2->addPackagingMaterial($packagingMaterial);
        }
        $manager->persist($product2);


        $manager->flush();
    }
}
