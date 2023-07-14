<?php

namespace App\Form\Type\Admin;

use App\Enum\Search\Data\Admin\UserSortEnum;
use App\Form\DataTransfer\Data\Admin\UserSearchDataInterface;
use App\Model\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin user search.
 */
class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.user_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => UserSortEnum::class,
                'label'        => 'form.admin.user_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    UserSortEnum::CREATED_AT_DESC => 'form.admin.user_search.sort_by.options.created_at_desc',
                    UserSortEnum::CREATED_AT_ASC  => 'form.admin.user_search.sort_by.options.created_at_asc',
                    UserSortEnum::EMAIL_ASC       => 'form.admin.user_search.sort_by.options.email_asc',
                    UserSortEnum::EMAIL_DESC      => 'form.admin.user_search.sort_by.options.email_desc',
                    UserSortEnum::NAME_ASC        => 'form.admin.user_search.sort_by.options.name_asc',
                    UserSortEnum::NAME_DESC       => 'form.admin.user_search.sort_by.options.name_desc',
                },
            ])
            ->add('role', EntityType::class, [
                'class'        => Role::class,
                'choice_label' => 'label',
                'choices'      => $options['choices_roles'],
                'placeholder'  => 'form.common.choice.irrelevant',
                'required'     => false,
                'label'        => 'form.admin.user_search.role',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => UserSearchDataInterface::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
            'choices_roles'      => null,
        ]);

        $resolver->setAllowedTypes('choices_roles', ['null', 'array']);
    }
}