<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\FormFieldSearchData;
use App\Library\Enum\Search\Data\Admin\FormFieldSortEnum;
use App\Service\Form\Type\Common\FormFieldTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin form field search.
 */
class FormFieldSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.form_field_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => FormFieldSortEnum::class,
                'label'        => 'form.admin.form_field_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    FormFieldSortEnum::CREATED_AT_DESC => 'form.admin.form_field_search.sort_by.options.created_at_desc',
                    FormFieldSortEnum::CREATED_AT_ASC  => 'form.admin.form_field_search.sort_by.options.created_at_asc',
                    FormFieldSortEnum::NAME_ASC        => 'form.admin.form_field_search.sort_by.options.name_asc',
                    FormFieldSortEnum::NAME_DESC       => 'form.admin.form_field_search.sort_by.options.name_desc',
                },
            ])
            ->add('type', FormFieldTypeType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'label'       => 'form.admin.form_field_search.type',
            ])
            ->add('isRequired', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices' => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.form_field_search.is_required',
            ])
            ->add('isGlobal', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices' => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.form_field_search.is_global',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => FormFieldSearchData::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}