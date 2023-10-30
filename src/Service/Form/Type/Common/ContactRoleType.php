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
            'choice_label' => fn ($choice) => match ($choice) {
                ContactRoleEnum::MOTHER  => 'contact_role.mother',
                ContactRoleEnum::FATHER  => 'contact_role.father',
                ContactRoleEnum::GRANDMA => 'contact_role.grandma',
                ContactRoleEnum::GRANDPA => 'contact_role.grandpa',
                ContactRoleEnum::AUNT    => 'contact_role.aunt',
                ContactRoleEnum::UNCLE   => 'contact_role.uncle',
                ContactRoleEnum::TUTOR   => 'contact_role.tutor',
                ContactRoleEnum::OTHER   => 'contact_role.other',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}