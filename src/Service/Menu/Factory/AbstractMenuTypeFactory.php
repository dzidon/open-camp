<?php

namespace App\Service\Menu\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractMenuTypeFactory implements MenuTypeFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {

    }
}