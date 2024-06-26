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
                'choice_label' => fn ($choice) => match ($choice)
                {
                    CampDateSortEnum::START_AT_ASC               => 'form.admin.camp_date_search.sort_by.options.start_at_asc',
                    CampDateSortEnum::START_AT_DESC              => 'form.admin.camp_date_search.sort_by.options.start_at_desc',
                    CampDateSortEnum::DEPOSIT_ASC                => 'form.admin.camp_date_search.sort_by.options.deposit_asc',
                    CampDateSortEnum::DEPOSIT_DESC               => 'form.admin.camp_date_search.sort_by.options.deposit_desc',
                    CampDateSortEnum::PRICE_WITHOUT_DEPOSIT_ASC  => 'form.admin.camp_date_search.sort_by.options.price_without_deposit_asc',
                    CampDateSortEnum::PRICE_WITHOUT_DEPOSIT_DESC => 'form.admin.camp_date_search.sort_by.options.price_without_deposit_desc',
                    CampDateSortEnum::CAPACITY_ASC               => 'form.admin.camp_date_search.sort_by.options.capacity_asc',
                    CampDateSortEnum::CAPACITY_DESC              => 'form.admin.camp_date_search.sort_by.options.capacity_desc',
                },
            ])
            ->add('isHistorical', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'choices'     => [
                    'form.admin.camp_date_search.is_historical.options.current_and_upcoming' => false,
                    'form.admin.camp_date_search.is_historical.options.historical'           => true,
                ],
                'label' => 'form.admin.camp_date_search.is_historical.label',
            ])
            ->add('isHidden', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices' => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_date_search.is_hidden',
            ])
            ->add('isOpenOnly', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices' => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_date_search.is_open_only',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => CampDateSearchData::class,
            'block_prefix'       => 'admin_camp_date_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}