<?php

namespace App\Tests\Form\Type\User;

use App\Form\DataTransfer\Data\User\BillingData;
use App\Form\Type\User\BillingType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class BillingTypeTest extends KernelTestCase
{
    public function testCountryChoices(): void
    {
        $factory = $this->getFormFactory();

        $data = new BillingData();
        $form = $factory->create(BillingType::class, $data);

        $choices = $form
            ->get('country')
            ->getConfig()
            ->getOption('choices')
        ;

        $expectedChoices = [
            'country.en' => 'en',
            'country.cs' => 'cs',
            'country.de' => 'de',
        ];

        $this->assertSame($expectedChoices, $choices);
    }

    private function getFormFactory(): FormFactoryInterface
    {
        $container = static::getContainer();

        /** @var FormFactoryInterface $factory */
        $factory = $container->get(FormFactoryInterface::class);

        return $factory;
    }
}