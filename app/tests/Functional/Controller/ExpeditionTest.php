<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Expedition;
use App\Repository\ExpeditionRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Tests\Common\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpeditionTest extends WebTestCase
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

    public function testGetExpeditions(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/expedition');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Expedition', (string) $response->getContent());
        $this->assertStringContainsString('Tee', (string)$response->getContent());
        $this->assertStringContainsString('10', (string)$response->getContent());
    }

    public function testAddExpedition(): void
    {
        $productRepository = static::getContainer()->get(ProductRepository::class);
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product 1 not found');
        $product1->setQuantity(1000);
        $productRepository->add($product1, true);
        $product2 = $productRepository->findOneBy(['name' => 'Coffee']) ?? throw new \Exception('Product 2 not found');
        $product2->setQuantity(1000);
        $productRepository->add($product2, true);


        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/expedition/add');

        $form = $crawler->selectButton($this->translator->trans('Save'))->form();

        $values = $form->getPhpValues();

        $values['expedition']['items'][0]['quantity'] = 1000;
        $values['expedition']['items'][0]['product'] = $product1->getId();
        $values['expedition']['items'][1]['quantity'] = 2000;
        $values['expedition']['items'][1]['product'] = $product2->getId();


        // Submit the form with the existing and new values.
        $crawler = $this->client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $this->assertResponseRedirects('/expedition');

        // Follow the redirect.
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('1000', $crawler->filter('td:contains("1000")')->text());
        $this->assertStringContainsString('2000', $crawler->filter('td:contains("2000")')->text());

        // Check if the product quantity has been updated
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $product2 = $productRepository->findOneBy(['name' => 'Coffee']) ?? throw new \Exception('Product not found');

        $this->assertEquals(0, $product1->getQuantity());
        $this->assertEquals(-1000, $product2->getQuantity());
    }

    public function testDeleteExpedition(): void
    {
        // Set product quantity to 1000
        $productRepository = static::getContainer()->get(ProductRepository::class);
        $product1 = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $product1->setQuantity(1000);
        $productRepository->add($product1, true);

        $expeditionRepository = static::getContainer()->get(ExpeditionRepository::class);
        // first expedition is created in the fixtures
        /** @var Expedition $expedition */
        $expedition = $expeditionRepository->findOneBy([], ['id' => 'asc']) ?? throw new \Exception(
            'Expedition not found'
        );

        $this->client->loginUser($this->user);

        $this->client->request('GET', '/expedition/'.$expedition->getId().'/delete');

        $this->assertResponseRedirects('/expedition');

        $product = $productRepository->findOneBy(['name' => 'Tee']) ?? throw new \Exception('Product not found');
        $this->assertEquals(1010, $product->getQuantity());
    }
}
