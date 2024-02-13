<?php

namespace App\Service\Form\Type\Common;

use App\Model\Enum\Entity\ContactRoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Contact role enum type.
 */
class ContactRoleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => ContactRoleEnum::class,
            'expanded'     => false,
            'multiple'     => false,
            'choice_label' => function (ContactRoleEnum $choice): string
            {
                return "contact_role.$choice->value";
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}