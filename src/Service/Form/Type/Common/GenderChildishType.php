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
            'choice_label' => function (GenderEnum $choice): string
            {
                return "gender_childish.$choice->value";
            },
        ]);
    }

    public function getParent(): string
    {
        return GenderType::class;
    }
}