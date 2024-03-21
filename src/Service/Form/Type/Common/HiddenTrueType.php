<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Minimal form used mainly for buttons.
 */
class HiddenTrueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('hiddenTrue', HiddenType::class, [
            'mapped' => false,
            'data'   => true,
        ]);
    }
}