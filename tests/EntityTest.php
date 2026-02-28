<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testUserInstantiation(): void
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testUserDefaultRoles(): void
    {
        $user = new User();
        $roles = $user->getRoles();
        $this->assertContains('ROLE_JTWC_USER', $roles);
    }

    public function testUserSettersGetters(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('test@example.com');
        $user->setFullname('Test User');

        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('Test User', $user->getFullname());
    }
}
