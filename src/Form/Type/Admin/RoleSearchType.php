<?php

namespace App\Form\Type\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\DataTransfer\Data\Admin\RoleSearchDataInterface;
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
                    RoleSortEnum::ID_DESC => 'form.admin.role_search.sort_by.options.id_desc',
                    RoleSortEnum::ID_ASC  => 'form.admin.role_search.sort_by.options.id_asc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => RoleSearchDataInterface::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}