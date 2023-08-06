<?php

namespace App\Tests\Service\Twig;

use App\Service\Twig\CountryNameExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Exception\MissingResourceException;

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

        $this->expectException(MissingResourceException::class);
        $extension->getCountryNameForCode('xx', 'en');
    }

    private function getCountryNameExtension(): CountryNameExtension
    {
        $container = static::getContainer();

        /** @var CountryNameExtension $extension */
        $extension = $container->get(CountryNameExtension::class);

        return $extension;
    }
}