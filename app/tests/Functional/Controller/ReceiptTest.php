<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Receipt;
use App\Repository\PackagingMaterialRepository;
use App\Repository\ReceiptRepository;
use App\Repository\UserRepository;
use App\Tests\Common\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReceiptTest extends WebTestCase
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

    public function testGetReceipts(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/receipt');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString($this->translator->trans('Receipts'), $crawler->filter('h5')->text());
        $this->assertStringContainsString('Packaging 1', $crawler->filter('td:contains("Packaging 1")')->text());
        $this->assertStringContainsString('10', $crawler->filter('td:contains("10")')->text());
    }

    public function testAddReceipt(): void
    {
        $packagingMaterialRepository = static::getContainer()->get(PackagingMaterialRepository::class);
        $packgingMaterial1 = $packagingMaterialRepository->findOneBy(['name' => 'Packaging 1']) ?? throw new \Exception(
            'Packaging Material 1 not found'
        );
        $packgingMaterial1->setQuantity(1000);
        $packagingMaterialRepository->add($packgingMaterial1, true);
        $packagingMaterial2 = $packagingMaterialRepository->findOneBy(['name' => 'Label 1']) ?? throw new \Exception(
            'Packaging Material 2 not found'
        );
        $packagingMaterial2->setQuantity(1000);
        $packagingMaterialRepository->add($packagingMaterial2, true);


        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/receipt/add');

        $form = $crawler->selectButton($this->translator->trans('Save'))->form();

        $values = $form->getPhpValues();

        $values['receipt']['items'][0]['quantity'] = 1000;
        $values['receipt']['items'][0]['packagingMaterial'] = $packgingMaterial1->getId();
        $values['receipt']['items'][1]['quantity'] = 2000;
        $values['receipt']['items'][1]['packagingMaterial'] = $packagingMaterial2->getId();


        // Submit the form with the existing and new values.
        $crawler = $this->client->request(
            $form->getMethod(),
            $form->getUri(),
            $values,
            $form->getPhpFiles()
        );

        $this->assertResponseRedirects('/receipt');

        // Follow the redirect.
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('1000', $crawler->filter('td:contains("1000")')->text());
        $this->assertStringContainsString('2000', $crawler->filter('td:contains("2000")')->text());

        // Check if the product quantity has been updated
        $packgingMaterial1 = $packagingMaterialRepository->findOneBy(['name' => 'Packaging 1']) ?? throw new \Exception(
            'Packaging Material not found'
        );
        $packagingMaterial2 = $packagingMaterialRepository->findOneBy(['name' => 'Label 1']) ?? throw new \Exception(
            'Packaging Material not found'
        );

        $this->assertEquals(2000, $packgingMaterial1->getQuantity());
        $this->assertEquals(3000, $packagingMaterial2->getQuantity());
    }

    public function testDeleteReceipt(): void
    {
        // Set packaging material quantity to 1000
        $packagingMaterialRepository = static::getContainer()->get(PackagingMaterialRepository::class);
        $packagingMaterial1 = $packagingMaterialRepository->findOneBy(
            ['name' => 'Packaging 1']
        ) ?? throw new \Exception(
            'Packaging Material not found'
        );
        $packagingMaterial1->setQuantity(1000);
        $packagingMaterialRepository->add($packagingMaterial1, true);

        $receiptRepository = static::getContainer()->get(ReceiptRepository::class);
        // first receipt is created in the fixtures
        /** @var Receipt $receipt */
        $receipt = $receiptRepository->findOneBy([], ['id' => 'asc']) ?? throw new \Exception(
            'Receipt not found'
        );

        $this->client->loginUser($this->user);

        $this->client->request('GET', '/receipt/'.$receipt->getId().'/delete');

        $this->assertResponseRedirects('/receipt');

        $product = $packagingMaterialRepository->findOneBy(['name' => 'Packaging 1']) ?? throw new \Exception(
            'Packaging Material not found'
        );
        $this->assertEquals(1010, $product->getQuantity());
    }
}
