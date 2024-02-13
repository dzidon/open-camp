<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\TripLocationPathSearchData;
use App\Library\Enum\Search\Data\Admin\TripLocationPathSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin trip location path search.
 */
class TripLocationPathSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.trip_location_path_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => TripLocationPathSortEnum::class,
                'label'        => 'form.admin.trip_location_path_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    TripLocationPathSortEnum::CREATED_AT_DESC => 'form.admin.trip_location_path_search.sort_by.options.created_at_desc',
                    TripLocationPathSortEnum::CREATED_AT_ASC  => 'form.admin.trip_location_path_search.sort_by.options.created_at_asc',
                    TripLocationPathSortEnum::NAME_ASC        => 'form.admin.trip_location_path_search.sort_by.options.name_asc',
                    TripLocationPathSortEnum::NAME_DESC       => 'form.admin.trip_location_path_search.sort_by.options.name_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => TripLocationPathSearchData::class,
            'block_prefix'       => 'admin_trip_location_path_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}