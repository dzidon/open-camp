<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp date search.
 */
class CampDateSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_date_search.from',
            ])
            ->add('to', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_date_search.to',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => CampDateSortEnum::class,
                'label'        => 'form.admin.camp_date_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    CampDateSortEnum::START_AT_ASC  => 'form.admin.camp_date_search.sort_by.options.start_at_asc',
                    CampDateSortEnum::START_AT_DESC => 'form.admin.camp_date_search.sort_by.options.start_at_desc',
                    CampDateSortEnum::PRICE_ASC     => 'form.admin.camp_date_search.sort_by.options.price_asc',
                    CampDateSortEnum::PRICE_DESC    => 'form.admin.camp_date_search.sort_by.options.price_desc',
                    CampDateSortEnum::CAPACITY_ASC  => 'form.admin.camp_date_search.sort_by.options.capacity_asc',
                    CampDateSortEnum::CAPACITY_DESC => 'form.admin.camp_date_search.sort_by.options.capacity_desc',
                },
            ])
            ->add('isHistorical', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'choices'     => [
                    'form.admin.camp_date_search.is_historical.choices.current_and_upcoming' => false,
                    'form.admin.camp_date_search.is_historical.choices.historical'           => true,
                ],
                'label' => 'form.admin.camp_date_search.is_historical.label',
            ])
            ->add('isActive', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices' => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_date_search.is_active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => CampDateSearchData::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}