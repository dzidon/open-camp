<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Checkbox that works with integers.
 */
class IntegerCheckboxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                function ($value)
                {
                    return (bool) $value;
                },
                function ($value)
                {
                    return (int) $value;
                }
            )
        );
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}