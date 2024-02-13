<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ContactSearchData;
use App\Library\Enum\Search\Data\User\ContactSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User contact search.
 */
class ContactSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.contact_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ContactSortEnum::class,
                'label'        => 'form.user.contact_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ContactSortEnum::CREATED_AT_DESC => 'form.user.contact_search.sort_by.options.created_at_desc',
                    ContactSortEnum::CREATED_AT_ASC  => 'form.user.contact_search.sort_by.options.created_at_asc',
                    ContactSortEnum::NAME_LAST_ASC   => 'form.user.contact_search.sort_by.options.name_last_asc',
                    ContactSortEnum::NAME_LAST_DESC  => 'form.user.contact_search.sort_by.options.name_last_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ContactSearchData::class,
            'block_prefix'       => 'user_contact_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}