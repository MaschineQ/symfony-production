<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\PackagingMaterialRepository;
use App\Repository\UserRepository;
use App\Tests\Common\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PackagingMaterialTest extends WebTestCase
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

    public function testGetPackagingMaterials(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/packaging-material');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Packaging 1', (string) $response->getContent());
        $this->assertStringContainsString('Label 1', (string) $response->getContent());
    }

    public function testAddPackagingMaterial(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/packaging-material/add');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm($this->translator->trans('Save'), [
            'packaging_material[name]' => 'Packaging 999',
            'packaging_material[quantity]' => 999,
        ]);

        $this->assertResponseRedirects('/packaging-material');

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Packaging 999', $crawler->filter('td:contains("Packaging 999")')->text());
    }

    public function testEditPackagingMaterial(): void
    {
        $packagingMaterialRepository = static::getContainer()->get(PackagingMaterialRepository::class);
        $packagingMaterial = $packagingMaterialRepository->findOneByName('Packaging 999') ?? throw new \Exception(
            'Packaging material not found'
        );

        $this->client->loginUser($this->user);

        $this->client->request('GET', '/packaging-material/'.$packagingMaterial->getId().'/edit');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm($this->translator->trans('Save'), [
            'packaging_material[name]' => 'Packaging 1000',
            'packaging_material[quantity]' => 1000,
        ]);

        $this->assertResponseRedirects('/packaging-material');

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Packaging 1000', $crawler->filter('td:contains("Packaging 1000")')->text());
    }

    public function testDeletePackagingMaterial(): void
    {
        $packagingMaterialRepository = static::getContainer()->get(PackagingMaterialRepository::class);
        $packagingMaterial = $packagingMaterialRepository->findOneByName('Packaging 1000') ?? throw new \Exception(
            'Packaging material not found'
        );

        $this->client->loginUser($this->user);

        $this->client->request('GET', '/packaging-material/'.$packagingMaterial->getId().'/delete');

        $this->assertResponseRedirects('/packaging-material');

        $crawler = $this->client->followRedirect();

        $this->assertStringNotContainsString(
            'Packaging 1000',
            $crawler->html()
        );
    }

    public function testDeletePackagingMaterialConstraint(): void
    {
        $packagingMaterialRepository = static::getContainer()->get(PackagingMaterialRepository::class);
        $packagingMaterial = $packagingMaterialRepository->findOneByName('Packaging 1') ?? throw new \Exception(
            'Packaging material not found'
        );

        $this->client->loginUser($this->user);

        $this->client->request('GET', '/packaging-material/'.$packagingMaterial->getId().'/delete');

        $crawler = $this->client->followRedirect();

        $errorMessage = $this->translator->trans('Packaging material cannot be deleted. It is used in other records.');
        $this->assertStringContainsString(
            $errorMessage,
            $crawler->filter('div:contains("'.$errorMessage.'")')->text()
        );
    }
}
