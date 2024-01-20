<?php

namespace App\Service\Form\Type\Common;

use App\Library\Enum\GenderEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Childish gender enum type.
 */
class GenderChildishType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => GenderEnum::class,
            'expanded'     => true,
            'multiple'     => false,
            'choice_label' => fn ($choice) => match ($choice) {
                GenderEnum::MALE   => 'gender_childish.m',
                GenderEnum::FEMALE => 'gender_childish.f',
            },
        ]);
    }

    public function getParent(): string
    {
        return GenderType::class;
    }
}