<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationContactSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationContactSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application contact search.
 */
class ApplicationContactSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.application_contact_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationContactSortEnum::class,
                'label'        => 'form.admin.application_contact_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationContactSortEnum::CREATED_AT_DESC => 'form.admin.application_contact_search.sort_by.options.created_at_desc',
                    ApplicationContactSortEnum::CREATED_AT_ASC  => 'form.admin.application_contact_search.sort_by.options.created_at_asc',
                    ApplicationContactSortEnum::NAME_LAST_ASC   => 'form.admin.application_contact_search.sort_by.options.name_last_asc',
                    ApplicationContactSortEnum::NAME_LAST_DESC  => 'form.admin.application_contact_search.sort_by.options.name_last_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationContactSearchData::class,
            'block_prefix'       => 'admin_application_contact_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}