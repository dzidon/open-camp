<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\UserSearchData;
use App\Library\Enum\Search\Data\Admin\UserSortEnum;
use App\Model\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Admin user search.
 */
class UserSearchType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        array_unshift($options['choices_roles'], false);

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
                    UserSortEnum::CREATED_AT_DESC     => 'form.admin.user_search.sort_by.options.created_at_desc',
                    UserSortEnum::CREATED_AT_ASC      => 'form.admin.user_search.sort_by.options.created_at_asc',
                    UserSortEnum::EMAIL_ASC           => 'form.admin.user_search.sort_by.options.email_asc',
                    UserSortEnum::EMAIL_DESC          => 'form.admin.user_search.sort_by.options.email_desc',
                    UserSortEnum::NAME_LAST_ASC       => 'form.admin.user_search.sort_by.options.name_last_asc',
                    UserSortEnum::NAME_LAST_DESC      => 'form.admin.user_search.sort_by.options.name_last_desc',
                    UserSortEnum::LAST_ACTIVE_AT_DESC => 'form.admin.user_search.sort_by.options.last_active_at_desc',
                },
            ])
            ->add('role', ChoiceType::class, [
                'choices'      => $options['choices_roles'],
                'choice_label' => function (false|Role $role)
                {
                    if ($role === false)
                    {
                        return $this->translator->trans('search.item_no_reference.female');
                    }

                    return $role->getLabel();
                },
                'placeholder'               => 'form.common.choice.irrelevant',
                'required'                  => false,
                'label'                     => 'form.admin.user_search.role',
                'choice_translation_domain' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => UserSearchData::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
            'choices_roles'      => [],
        ]);

        $resolver->setAllowedTypes('choices_roles', ['array']);
    }
}