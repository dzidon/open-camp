<?php

namespace App\Form\Type\Common;

use App\Enum\GenderEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Gender enum type.
 */
class GenderType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => GenderEnum::class,
            'expanded'     => true,
            'multiple'     => false,
            'choice_label' => fn ($choice) => match ($choice) {
                GenderEnum::MALE   => 'gender.m',
                GenderEnum::FEMALE => 'gender.f',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}