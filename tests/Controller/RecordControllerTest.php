<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Record;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\RecordRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordControllerTest extends WebTestCase
{
    /**
     * Test '/record' route.
     */
    public const TEST_ROUTE = '/record';

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
    public function testRecordRoute(): void
    {
        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test show record.
     *
     * @return void void
     */
    public function testShowRecord(): void
    {
        $user = $this->createUser('record1');
        $this->httpClient->loginUser($user);
        // given
        $expectedRecord = new Record();
        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        $testCategory  = new Category();
        $testCategory->setTitle('category1');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);

        $expectedRecord->setTitle('Record one');
        $expectedRecord->setText('Test Text1');
        $expectedRecord->setVisibility('1');
        $expectedRecord->setCategory($testCategory);
        $expectedRecord->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedRecord->setUpdatedAt(new \DateTimeImmutable('now'));
        $recordRepository = static::getContainer()->get(RecordRepository::class);
        $recordRepository->save($expectedRecord);

        // when
        $this->httpClient->request('GET', '/record/'.$expectedRecord->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Record message.
     *
     * @return void void
     */
    public function testCategoryMessage(): void
    {
        // when
        $this->httpClient->request('GET', '/record');
        $result = $this->httpClient->getResponse()->getContent();

        // then
        $this->assertStringContainsString('Lista recordów', $result);
    }


    /**
     * Test delete record.
     *
     * @return void void
     */
    public function testDeleteRecord(): void
    {
        // given
        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        $testCategory  = new Category();
        $testCategory->setTitle('category2');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);
        $user = $this->createUser('record2');
        $this->httpClient->loginUser($user);
        $recordRepository =
            static::getContainer()->get(RecordRepository::class);
        $testRecord= new Record();
        $testRecord->setTitle('Record two');
        $testRecord->setCreatedAt(new \DateTimeImmutable('now'));
        $testRecord->setUpdatedAt(new \DateTimeImmutable('now'));
        $testRecord->setText('Test Text2');
        $testRecord->setVisibility('1');

        $testRecord->setCategory($testCategory);
        $testRecord->setUser($user);
        $recordRepository->save($testRecord);
        $testRecordId = $testRecord->getId();
        $this->httpClient->request('GET', self::TEST_ROUTE.'/delete/'.$testRecordId);

        // when
        $this->httpClient->submitForm(
            'Usuwanie'
        );

        // then
        $this->assertNull($recordRepository->findByTitle('Record two'));
    }

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
