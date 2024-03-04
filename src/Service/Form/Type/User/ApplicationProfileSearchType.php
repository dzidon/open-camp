<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationProfileSearchData;
use App\Library\Enum\Search\Data\User\ApplicationProfileSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User profile application search.
 */
class ApplicationProfileSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.application_profile_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationProfileSortEnum::class,
                'label'        => 'form.user.application_profile_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationProfileSortEnum::COMPLETED_AT_DESC => 'form.user.application_profile_search.sort_by.options.completed_at_desc',
                    ApplicationProfileSortEnum::COMPLETED_AT_ASC  => 'form.user.application_profile_search.sort_by.options.completed_at_asc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationProfileSearchData::class,
            'block_prefix'       => 'user_application_profile_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}