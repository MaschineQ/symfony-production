<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Production;
use App\Repository\ProductionRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Tests\Common\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductionTest extends WebTestCase
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

    public function testGetProductions(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/production');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Tee', (string)$response->getContent());
        $this->assertStringContainsString('10', (string)$response->getContent());
    }

    public function testAddProduction(): void
    {
        $productRepository = static::getContainer()->get(ProductRepository::class);
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $product1->setQuantity(1000);
        $productRepository->add($product1, true);
        $product2 = $productRepository->findOneBy(['name' => 'Coffee']) ?? throw new \Exception('Product not found');
        $product2->setQuantity(1000);
        $productRepository->add($product2, true);


        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/production/add');

        $form = $crawler->selectButton($this->translator->trans('Save'))->form();

        $values = $form->getPhpValues();

        $values['production']['items'][0]['quantity'] = 1000;
        $values['production']['items'][0]['product'] = $product1->getId();
        $values['production']['items'][1]['quantity'] = 2000;
        $values['production']['items'][1]['product'] = $product2->getId();


        // Submit the form with the existing and new values.
        $crawler = $this->client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $this->assertResponseRedirects('/production');

        // Follow the redirect.
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('1000', $crawler->filter('td:contains("1000")')->text());
        $this->assertStringContainsString('2000', $crawler->filter('td:contains("2000")')->text());

        // Check if the product quantity has been updated
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $product2 = $productRepository->findOneBy(['name' => 'Coffee']) ?? throw new \Exception('Product not found');

        $this->assertEquals(1100, $product1->getQuantity());
        $this->assertEquals(1200, $product2->getQuantity());
    }

    public function testAddProductionWithInvalidQuantity(): void
    {
        $productRepository = static::getContainer()->get(ProductRepository::class);
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');


        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/production/add');

        $form = $crawler->selectButton($this->translator->trans('Save'))->form();

        $values = $form->getPhpValues();

        $values['production']['items'][0]['quantity'] = 1;
        $values['production']['items'][0]['product'] = $product1->getId();

        $crawler = $this->client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $errorMessage = $this->translator->trans(
            'The quantity does not match the package.',
            [
                '%quantityPerPiece%' => $product1->getQuantityPerPiece(),
                '%packagingType%' => $product1->getPackagingType()
            ],
            'validators'
        );
        $this->assertStringContainsString(
            $errorMessage,
            $crawler->filter('div > ul > li:contains("'.$errorMessage.'")')->text()
        );
    }

    public function testDeleteProduction(): void
    {
        // Set product quantity to 1000
        $productRepository = static::getContainer()->get(ProductRepository::class);
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $product1->setQuantity(1000);
        $productRepository->add($product1, true);

        $productionRepository = static::getContainer()->get(ProductionRepository::class);
        // first production is created in the fixtures
        /** @var Production $production */
        $production = $productionRepository->findOneBy([], ['id' => 'asc']) ?? throw new \Exception(
            'Production not found'
        );

        $this->client->loginUser($this->user);

        $this->client->request('GET', '/production/'.$production->getId().'/delete');

        $this->assertResponseRedirects('/production');

        $product = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $this->assertEquals(999, $product->getQuantity());
    }
}
