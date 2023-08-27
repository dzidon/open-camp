<?php

namespace App\Tests\Service\Form\Extension;

use App\Service\Form\Extension\CountryTypeDefaultsExtension;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Test\TypeTestCase;

class CountryTypeDefaultsExtensionTest extends TypeTestCase
{
    public function testDefaults(): void
    {
        $form = $this->factory->create(CountryType::class);
        $config = $form->getConfig();

        $preferredCountries = $config->getOption('preferred_choices');
        $this->assertSame(['CZ', 'SK', 'DE'], $preferredCountries);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new CountryTypeDefaultsExtension(['CZ', 'SK', 'DE']),
        ];
    }
}