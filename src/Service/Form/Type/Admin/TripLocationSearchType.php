<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\TripLocationSearchData;
use App\Library\Enum\Search\Data\Admin\TripLocationSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin trip location search.
 */
class TripLocationSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.trip_location_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => TripLocationSortEnum::class,
                'label'        => 'form.admin.trip_location_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    TripLocationSortEnum::CREATED_AT_DESC => 'form.admin.trip_location_search.sort_by.options.created_at_desc',
                    TripLocationSortEnum::CREATED_AT_ASC  => 'form.admin.trip_location_search.sort_by.options.created_at_asc',
                    TripLocationSortEnum::NAME_ASC        => 'form.admin.trip_location_search.sort_by.options.name_asc',
                    TripLocationSortEnum::NAME_DESC       => 'form.admin.trip_location_search.sort_by.options.name_desc',
                    TripLocationSortEnum::PRICE_ASC       => 'form.admin.trip_location_search.sort_by.options.price_asc',
                    TripLocationSortEnum::PRICE_DESC      => 'form.admin.trip_location_search.sort_by.options.price_desc',
                    TripLocationSortEnum::PRIORITY_ASC    => 'form.admin.trip_location_search.sort_by.options.priority_asc',
                    TripLocationSortEnum::PRIORITY_DESC   => 'form.admin.trip_location_search.sort_by.options.priority_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => TripLocationSearchData::class,
            'block_prefix'       => 'admin_trip_location_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}