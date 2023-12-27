<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateFormFieldData;
use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\TripLocationPath;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp date editing.
 */
class CampDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'label'  => 'form.admin.camp_date.start_at',
            ])
            ->add('endAt', DateTimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'label'  => 'form.admin.camp_date.end_at',
            ])
            ->add('deposit', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5' => true,
                'label' => 'form.admin.camp_date.deposit',
            ])
            ->add('priceWithoutDeposit', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5' => true,
                'label' => 'form.admin.camp_date.price_without_deposit',
            ])
            ->add('capacity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'label' => 'form.admin.camp_date.capacity',
            ])
            ->add('isOpenAboveCapacity', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.is_open_above_capacity',
            ])
            ->add('isClosed', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.is_closed',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.description',
            ])
            ->add('leaders', UserAutocompleteType::class, [
                'required' => false,
                'multiple' => true,
                'label'    => 'form.admin.camp_date.leaders',
            ])
            ->add('tripLocationPathThere', EntityType::class, [
                'class'        => TripLocationPath::class,
                'choice_label' => 'name',
                'choices'      => $options['choices_trip_location_paths'],
                'placeholder'  => 'form.common.choice.none.female',
                'required'     => false,
                'label'        => 'form.admin.camp_date.trip_location_path_there',
            ])
            ->add('tripLocationPathBack', EntityType::class, [
                'class'        => TripLocationPath::class,
                'choice_label' => 'name',
                'choices'      => $options['choices_trip_location_paths'],
                'placeholder'  => 'form.common.choice.none.female',
                'required'     => false,
                'label'        => 'form.admin.camp_date.trip_location_path_back',
            ])
            ->add('campDateFormFieldsData', CollectionType::class, [
                'entry_type'                  => CampDateFormFieldType::class,
                'label'                       => 'form.admin.camp_date.form_fields',
                'suppress_required_rendering' => true,
                'allow_add'                   => true,
                'allow_delete'                => true,
                'entry_options'               => [
                    'label'               => false,
                    'remove_button'       => true,
                    'choices_form_fields' => $options['choices_form_fields'],
                ],
                'prototype_options' => [
                    'remove_button'       => true,
                    'choices_form_fields' => $options['choices_form_fields'],
                ],
                'prototype_data' => new CampDateFormFieldData(),
            ])
            ->add('addCampDateFormFieldData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.camp_date.add_form_field',
                'collection_name' => 'campDateFormFieldsData',
            ])
            ->add('campDateAttachmentConfigsData', CollectionType::class, [
                'entry_type'                  => CampDateAttachmentConfigType::class,
                'label'                       => 'form.admin.camp_date.attachment_configs',
                'suppress_required_rendering' => true,
                'allow_add'                   => true,
                'allow_delete'                => true,
                'entry_options'               => [
                    'label'                      => false,
                    'remove_button'              => true,
                    'choices_attachment_configs' => $options['choices_attachment_configs'],
                ],
                'prototype_options' => [
                    'remove_button'              => true,
                    'choices_attachment_configs' => $options['choices_attachment_configs'],
                ],
                'prototype_data' => new CampDateAttachmentConfigData(),
            ])
            ->add('addCampDateAttachmentConfigData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.camp_date.add_attachment_config',
                'collection_name' => 'campDateAttachmentConfigsData',
            ])
            ->add('campDatePurchasableItemsData', CollectionType::class, [
                'entry_type'                  => CampDatePurchasableItemType::class,
                'label'                       => 'form.admin.camp_date.purchasable_items',
                'suppress_required_rendering' => true,
                'allow_add'                   => true,
                'allow_delete'                => true,
                'entry_options'               => [
                    'label'                     => false,
                    'remove_button'             => true,
                    'choices_purchasable_items' => $options['choices_purchasable_items'],
                ],
                'prototype_options' => [
                    'remove_button'             => true,
                    'choices_purchasable_items' => $options['choices_purchasable_items'],
                ],
                'prototype_data' => new CampDatePurchasableItemData(),
            ])
            ->add('addCampDatePurchasableItemData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.camp_date.add_purchasable_item',
                'collection_name' => 'campDatePurchasableItemsData',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'                  => CampDateData::class,
            'choices_trip_location_paths' => [],
            'choices_form_fields'         => [],
            'choices_attachment_configs'  => [],
            'choices_purchasable_items'   => [],
        ]);

        $resolver->setAllowedTypes('choices_trip_location_paths', ['array']);
        $resolver->setAllowedTypes('choices_form_fields', ['array']);
        $resolver->setAllowedTypes('choices_attachment_configs', ['array']);
        $resolver->setAllowedTypes('choices_purchasable_items', ['array']);
    }
}