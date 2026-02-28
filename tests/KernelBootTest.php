<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KernelBootTest extends KernelTestCase
{
    public function testKernelBoots(): void
    {
        self::bootKernel();
        $this->assertNotNull(self::$kernel);
    }

    public function testContainerIsAvailable(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->assertNotNull($container);
    }
}
