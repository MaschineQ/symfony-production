<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Tests\Common\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryTest extends WebTestCase
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

    public function testGetCategories(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/category');

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Drinks', (string) $response->getContent());
    }

    public function testCategoryForm(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/category/add');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm($this->translator->trans('Save'), [
            'category[name]' => 'Cars',
        ]);

        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        /** @var Category $category */
        $category = $categoryRepository->findOneByName('Cars');
        $this->assertEquals(
            'Cars',
            $category->getName()
        );
    }
}
