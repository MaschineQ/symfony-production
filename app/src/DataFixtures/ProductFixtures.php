<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\PackagingMaterial;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Util\Exception;

class ProductFixtures extends Fixture
{
    public function getDependencies(): array
    {
        return [PackagingMaterialFixtures::class];
    }
// todo fixtures
    public function load(ObjectManager $manager): void
    {
        $category = $manager->getRepository(Category::class);
        $category1 = $category->findOneBy(['name' => 'Agro']) ?? throw new Exception('Category not found');
        $category2 = $category->findOneBy(['name' => 'Cosmetics']) ?? throw new Exception('Category not found');
        $packagingMaterial = $manager->getRepository(PackagingMaterial::class);
        $pack = new PackagingMaterial();
        $pack->setName('Packaging 1');

        $product1 = new Product();
        $product1->setName('Fertilizer 1');
        $product1->setCategory($category1);
        $product1->setPackagingType('l');
        $product1->setQuantityPerPiece(10);
        $product1->getPackagingMaterial()->add(
            $packagingMaterial->findOneByName('Packaging 1') ?? throw new Exception('Packaging material not found')
        );
        $product1->getPackagingMaterial()->add(
            $packagingMaterial->findOneByName('Label 1') ?? throw new Exception('Packaging material not found')
        );
        $product1->setQuantity(100);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Fertilizer 2');
        $product2->setCategory($category1);
        $product2->setPackagingType('l');
        $product2->setQuantityPerPiece(10);
        $product2->addPackagingMaterial(
            $packagingMaterial->findOneByName('Packaging 2') ?? throw new Exception('Packaging material not found')
        );
        $product2->addPackagingMaterial(
            $packagingMaterial->findOneByName('Label 2') ?? throw new Exception('Packaging material not found')
        );
        $product2->setQuantity(100);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Cream 1');
        $product3->setCategory($category2);
        $product3->setPackagingType('g');
        $product3->setQuantityPerPiece(10);
        $product3->addPackagingMaterial(
            $packagingMaterial->findOneByName('Packaging 3') ?? throw new Exception('Packaging material not found')
        );
        $product3->addPackagingMaterial(
            $packagingMaterial->findOneByName('Label 3') ?? throw new Exception('Packaging material not found')
        );
        $product3->setQuantity(100);
        $manager->persist($product3);

        $manager->flush();
    }
}
