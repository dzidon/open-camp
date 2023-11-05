<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateFormFieldData;
use App\Model\Entity\FormField;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp date form field type.
 */
class CampDateFormFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formField', EntityType::class, [
                'class'            => FormField::class,
                'choice_label'     => 'name',
                'choices'          => $options['choices_form_fields'],
                'label'            => 'form.admin.camp_date_form_field.form_field',
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.camp_date_form_field.priority',
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionItemType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'          => CampDateFormFieldData::class,
            'choices_form_fields' => [],
        ]);

        $resolver->setAllowedTypes('choices_form_fields', ['array']);
    }
}