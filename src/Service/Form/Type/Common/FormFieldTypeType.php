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
            'choice_label' => fn ($choice) => match ($choice) {
                FormFieldTypeEnum::TEXT      => 'form_field_type.text',
                FormFieldTypeEnum::TEXT_AREA => 'form_field_type.text_area',
                FormFieldTypeEnum::NUMBER    => 'form_field_type.number',
                FormFieldTypeEnum::CHOICE    => 'form_field_type.choice',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}