<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\DataFixtures\PackagingMaterialFixtures;
use App\Entity\Category;
use App\Entity\PackagingMaterial;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\PackagingMaterialRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Tests\Common\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductTest extends WebTestCase
{
    private ?UserRepository $userRepository;
    private KernelBrowser $client;
    private UserInterface $user;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->user = $this->userRepository->findOneByEmail(UserFixtures::FIRST_USER) ?? throw new \Exception(
            'User not found'
        );
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    public function testGetProducts(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/product');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Tee', (string) $response->getContent());
    }

    public function testAddProduct(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/product/add');

        $this->assertResponseIsSuccessful();


        /** @var Category $category */
        $category = static::getContainer()->get(CategoryRepository::class)->findOneByName('Drinks');
        /** @var PackagingMaterial $packagingMaterial */
        $packagingMaterial = static::getContainer()->get(PackagingMaterialRepository::class)->findOneByName(
            PackagingMaterialFixtures::FIRST_PACKAGING_MATERIAL_LABEL
        );

        $form = $this->client->submitForm($this->translator->trans('Save'), [
            'product[name]' => 'Beer',
            'product[packagingType]' => 'l',
            'product[quantityPerPiece]' => 10,
            'product[category]' => $category->getId(),
            'product[packagingMaterial]' => $packagingMaterial->getId(),
        ]);

        $productRepository = static::getContainer()->get(ProductRepository::class);
        /** @var Product $product */
        $product = $productRepository->findOneByName('Beer');

        $this->assertEquals('Beer', $product->getName());
        $this->assertEquals('l', $product->getPackagingType());
        $this->assertEquals('10', $product->getQuantityPerPiece());
        $this->assertEquals('Drinks', $category->getName());
        $this->assertEquals(PackagingMaterialFixtures::FIRST_PACKAGING_MATERIAL_LABEL, $packagingMaterial->getName());
    }
}
