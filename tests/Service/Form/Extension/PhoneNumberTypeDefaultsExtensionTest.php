<?php

namespace App\Tests\Service\Form\Extension;

use App\Service\Form\Extension\PhoneNumberTypeDefaultsExtension;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\Test\TypeTestCase;

class PhoneNumberTypeDefaultsExtensionTest extends TypeTestCase
{
    public function testDefaults(): void
    {
        $form = $this->factory->create(PhoneNumberType::class);
        $config = $form->getConfig();

        $defaultRegion = $config->getOption('default_region');
        $this->assertSame('CZ', $defaultRegion);

        $defaultFormat = $config->getOption('format');
        $this->assertSame(1, $defaultFormat);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new PhoneNumberTypeDefaultsExtension('CZ', 1),
        ];
    }
}