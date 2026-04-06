<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends WebTestCase
{
    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        // Create test user
        $user = new User();
        $user->setEmail('test.login@biblioconnect.fr')
             ->setFirstName('Test')
             ->setLastName('Login')
             ->setPassword($hasher->hashPassword($user, 'Test1234!'));

        $em->persist($user);
        $em->flush();

        // Try to login
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Se connecter', [
            '_username' => 'test.login@biblioconnect.fr',
            '_password' => 'Test1234!',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            '_username' => 'fake@email.com',
            '_password' => 'wrongpassword',
        ]);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testAdminRedirectAfterLogin(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $admin = new User();
        $admin->setEmail('test.admin@biblioconnect.fr')
              ->setFirstName('Admin')
              ->setLastName('Test')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($hasher->hashPassword($admin, 'Admin123!'));

        $em->persist($admin);
        $em->flush();

        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'test.admin@biblioconnect.fr',
            '_password' => 'Admin123!',
        ]);

        $client->followRedirects();
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful() || $response->isRedirection());
    }
}
