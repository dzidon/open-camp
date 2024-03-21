<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationCampDateSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationCampDateSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application camp date search.
 */
class ApplicationCampDateSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.application_camp_date_search.from',
            ])
            ->add('to', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.application_camp_date_search.to',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationCampDateSortEnum::class,
                'label'        => 'form.admin.application_camp_date_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationCampDateSortEnum::CAMP_DATE_START_AT_ASC              => 'form.admin.application_camp_date_search.sort_by.options.camp_date_start_at_asc',
                    ApplicationCampDateSortEnum::CAMP_DATE_START_AT_DESC             => 'form.admin.application_camp_date_search.sort_by.options.camp_date_start_at_desc',
                    ApplicationCampDateSortEnum::NUMBER_OF_PENDING_APPLICATIONS_DESC => 'form.admin.application_camp_date_search.sort_by.options.number_of_pending_applications_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationCampDateSearchData::class,
            'block_prefix'       => 'admin_application_camp_date_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}