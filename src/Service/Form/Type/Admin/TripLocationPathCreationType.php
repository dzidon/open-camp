<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin trip location path creation.
 */
class TripLocationPathCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tripLocationPathData', TripLocationPathType::class, [
                'label' => false,
            ])
            ->add('tripLocationsData', CollectionType::class, [
                'entry_type'                  => TripLocationType::class,
                'label'                       => 'form.admin.trip_location_path_creation.trip_locations',
                'suppress_required_rendering' => true,
                'allow_add'                   => true,
                'allow_delete'                => true,
                'by_reference'                => false,
                'entry_options'               => [
                    'label'         => false,
                    'remove_button' => true,
                ],
                'prototype_options' => [
                    'remove_button' => true,
                ],
            ])
            ->add('addTripLocationData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.trip_location_path_creation.add_trip_location',
                'collection_name' => 'tripLocationsData',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TripLocationPathCreationData::class,
        ]);
    }
}