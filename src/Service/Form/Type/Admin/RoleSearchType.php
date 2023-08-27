<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\RoleSearchData;
use App\Library\Enum\Search\Data\Admin\RoleSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin role search.
 */
class RoleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.role_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => RoleSortEnum::class,
                'label'        => 'form.admin.role_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    RoleSortEnum::CREATED_AT_DESC => 'form.admin.role_search.sort_by.options.created_at_desc',
                    RoleSortEnum::CREATED_AT_ASC  => 'form.admin.role_search.sort_by.options.created_at_asc',
                    RoleSortEnum::LABEL_ASC       => 'form.admin.role_search.sort_by.options.label_asc',
                    RoleSortEnum::LABEL_DESC      => 'form.admin.role_search.sort_by.options.label_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => RoleSearchData::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}