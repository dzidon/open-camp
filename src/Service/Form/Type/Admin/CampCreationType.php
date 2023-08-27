<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampCreationData;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp creation.
 */
class CampCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campData', CampType::class, [
                'choices_camp_categories' => $options['choices_camp_categories'],
                'label'                   => false,
            ])
            ->add('images', FileType::class, [
                'required' => false,
                'multiple' => true,
                'label'    => 'form.admin.camp_creation.images',
            ])
            ->add('campDatesData', CollectionType::class, [
                'entry_type'                  => CampDateType::class,
                'label'                       => 'form.admin.camp_creation.camp_dates',
                'suppress_required_rendering' => true,
                'allow_add'                   => true,
                'allow_delete'                => true,
                'entry_options'               => [
                    'label'         => false,
                    'remove_button' => true,
                ],
                'prototype_options' => [
                    'remove_button' => true,
                ],
            ])
            ->add('addCampDateData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.camp_creation.add_camp_date',
                'collection_name' => 'campDatesData',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampCreationData::class,
            'choices_camp_categories' => [],
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}