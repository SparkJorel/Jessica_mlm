<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertLessThan(500, $client->getResponse()->getStatusCode());
    }

    public function testRedirectIfNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/dashboard');

        $this->assertTrue($client->getResponse()->isRedirection());
    }
}
