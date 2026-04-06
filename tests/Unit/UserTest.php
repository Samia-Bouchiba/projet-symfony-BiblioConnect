<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setEmail('jean.dupont@test.fr');

        $this->assertSame('Jean', $user->getFirstName());
        $this->assertSame('Dupont', $user->getLastName());
        $this->assertSame('Jean Dupont', $user->getFullName());
        $this->assertSame('jean.dupont@test.fr', $user->getEmail());
        $this->assertSame('jean.dupont@test.fr', $user->getUserIdentifier());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertNull($user->getId());
        $this->assertTrue($user->isActive());
    }

    public function testDefaultRoles(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertCount(1, $user->getRoles());
    }

    public function testAdminRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testLibrarianRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_LIBRARIAN']);
        $this->assertContains('ROLE_LIBRARIAN', $user->getRoles());
    }

    public function testPasswordStorage(): void
    {
        $user = new User();
        $user->setPassword('hashed_password_123');
        $this->assertSame('hashed_password_123', $user->getPassword());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setPassword('secret');
        $user->eraseCredentials();
        // eraseCredentials does nothing here; password still intact (stored hash)
        $this->assertSame('secret', $user->getPassword());
    }

    public function testIsActiveDefault(): void
    {
        $user = new User();
        $this->assertTrue($user->isActive());
    }

    public function testCreatedAtIsSet(): void
    {
        $user = new User();
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }
}
