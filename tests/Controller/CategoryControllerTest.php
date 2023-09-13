<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    /**
     * Test '/category' route.
     */
    public const TEST_ROUTE = '/category';

    /**
     * Test client.
     */
    protected KernelBrowser $httpClient;

    /**
     * @return void void
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test route.
     *
     * @return void void
     */
    public function testCategoryRoute(): void
    {
        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test show category.
     *
     * @return void void
     */
    public function testShowCategory(): void
    {
        // given
        $expectedCategory = new Category();
        $expectedCategory->setTitle('Category one');
        $expectedCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $categoryRepository->save($expectedCategory);

        // when
        $this->httpClient->request('GET', '/category/'.$expectedCategory->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Category message.
     *
     * @return void void
     */
    public function testCategoryMessage(): void
    {
        // when
        $this->httpClient->request('GET', '/category');
        $result = $this->httpClient->getResponse()->getContent();

        // then
        $this->assertStringContainsString('Lista kategorii', $result);
    }


    /**
     * Test delete category.
     *
     * @return void void
     */

    /**
     * Create user.
     *
     * @param string $name name
     *
     * @return User User entity
     */
    protected function createUser(string $name): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail($name.'@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'admin123'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }
}
