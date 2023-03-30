<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\PackagingMaterial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PackagingMaterialFixtures extends Fixture implements FixtureGroupInterface
{
    public const FIRST_PACKAGING_MATERIAL_PACKAGING = 'Packaging 1';
    public const SECOND_PACKAGING_MATERIAL_PACKAGING = 'Packaging 2';
    public const THIRD_PACKAGING_MATERIAL_PACKAGING = 'Packaging 3';
    public const FIRST_PACKAGING_MATERIAL_LABEL = 'Label 1';
    public const SECOND_PACKAGING_MATERIAL_LABEL = 'Label 2';
    public const THIRD_PACKAGING_MATERIAL_LABEL = 'Label 3';

    public static function getGroups(): array
    {
        return ['test-data'];
    }

    public function load(ObjectManager $manager): void
    {
        $packagingMaterial = new PackagingMaterial();
        $packagingMaterial->setName('Packaging 1');
        $packagingMaterial->setQuantity(10);
        $manager->persist($packagingMaterial);
        $manager->flush();

        $packagingMaterial = new PackagingMaterial();
        $packagingMaterial->setName('Packaging 2');
        $packagingMaterial->setQuantity(100);
        $manager->persist($packagingMaterial);
        $manager->flush();

        $packagingMaterial = new PackagingMaterial();
        $packagingMaterial->setName('Packaging 3');
        $packagingMaterial->setQuantity(100);
        $manager->persist($packagingMaterial);
        $manager->flush();

        $packagingMaterial = new PackagingMaterial();
        $packagingMaterial->setName('Label 1');
        $packagingMaterial->setQuantity(10);
        $manager->persist($packagingMaterial);
        $manager->flush();

        $packagingMaterial = new PackagingMaterial();
        $packagingMaterial->setName('Label 2');
        $packagingMaterial->setQuantity(100);
        $manager->persist($packagingMaterial);
        $manager->flush();

        $packagingMaterial = new PackagingMaterial();
        $packagingMaterial->setName('Label 3');
        $packagingMaterial->setQuantity(100);
        $manager->persist($packagingMaterial);
        $manager->flush();
    }
}
