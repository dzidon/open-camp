<?php

namespace App\Menu\Factory;

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