<?php

namespace App\Form\Type\Common;

use App\Enum\Entity\ContactRoleEnum;
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
            'expanded'     => true,
            'multiple'     => false,
            'choice_label' => fn ($choice) => match ($choice) {
                ContactRoleEnum::MOTHER => 'contact_role.mother',
                ContactRoleEnum::FATHER => 'contact_role.father',
                ContactRoleEnum::TUTOR  => 'contact_role.tutor',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}