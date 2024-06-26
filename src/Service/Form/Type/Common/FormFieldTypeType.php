<?php

namespace App\Service\Form\Type\Common;

use App\Model\Enum\Entity\FormFieldTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form field type enum type.
 */
class FormFieldTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => FormFieldTypeEnum::class,
            'expanded'     => false,
            'multiple'     => false,
            'choice_label' => function (FormFieldTypeEnum $choice): string
            {
                return "form_field_type.$choice->value";
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}