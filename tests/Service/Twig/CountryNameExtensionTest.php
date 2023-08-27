<?php

namespace App\Tests\Service\Twig;

use App\Service\Twig\CountryNameExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CountryNameExtensionTest extends KernelTestCase
{
    public function testGetCountryNameForCode(): void
    {
        $extension = $this->getCountryNameExtension();

        $countryName = $extension->getCountryNameForCode('CZ', 'en');
        $this->assertSame('Czechia', $countryName);
    }

    public function testGetCountryNameForInvalidCode(): void
    {
        $extension = $this->getCountryNameExtension();

        $this->assertEmpty($extension->getCountryNameForCode('xx', 'en'));
    }

    private function getCountryNameExtension(): CountryNameExtension
    {
        $container = static::getContainer();

        /** @var CountryNameExtension $extension */
        $extension = $container->get(CountryNameExtension::class);

        return $extension;
    }
}