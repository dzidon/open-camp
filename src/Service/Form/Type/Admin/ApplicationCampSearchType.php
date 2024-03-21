<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationCampSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationCampSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application camp search.
 */
class ApplicationCampSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.application_camp_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationCampSortEnum::class,
                'label'        => 'form.admin.application_camp_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationCampSortEnum::CAMP_NAME_ASC                       => 'form.admin.application_camp_search.sort_by.options.camp_name_asc',
                    ApplicationCampSortEnum::CAMP_NAME_DESC                      => 'form.admin.application_camp_search.sort_by.options.camp_name_desc',
                    ApplicationCampSortEnum::NUMBER_OF_PENDING_APPLICATIONS_DESC => 'form.admin.application_camp_search.sort_by.options.number_of_pending_applications_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationCampSearchData::class,
            'block_prefix'       => 'admin_application_camp_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}