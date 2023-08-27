<?php

namespace App\Tests\Service\Form\Extension;

use App\Service\Form\Extension\MoneyTypeDefaultsExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Test\TypeTestCase;

class MoneyTypeDefaultsExtensionTest extends TypeTestCase
{
    public function testDefaults(): void
    {
        $form = $this->factory->create(MoneyType::class);
        $config = $form->getConfig();

        $currency = $config->getOption('currency');
        $this->assertSame('CZK', $currency);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new MoneyTypeDefaultsExtension('CZK'),
        ];
    }
}