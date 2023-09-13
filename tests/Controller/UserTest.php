<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserTest extends WebTestCase
{
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
     * Test show user.
     */
    public function testShowUser(): void
    {
        // given
        $user = $this->createUser('user_a');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', '/'.$user->getId().'/show');
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * Test edit email.
     */
    public function testEditEmailUser(): void
    {
        // given
        $user = $this->createUser('user_b');
        $this->httpClient->loginUser($user);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $newEmail = 'newemail_b@example.com';

        // when
        $this->httpClient->request('GET', '/'.
            $user->getId().'/edit/email');

        $this->httpClient->submitForm(
            'Zapisanie',
            ['user' => ['email' => $newEmail]]
        );

        // then
        $savedUser = $userRepository->findOneById($user->getId());
        $this->assertEquals($newEmail, $savedUser->getEmail());
    }

    /**
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
