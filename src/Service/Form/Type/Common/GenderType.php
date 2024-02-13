<?php

namespace App\Service\Form\Type\Common;

use App\Library\Enum\GenderEnum;
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
            'choice_label' => function (GenderEnum $choice): string
            {
                return "gender.$choice->value";
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}