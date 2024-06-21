<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateFormFieldData;
use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Library\Data\Admin\CampDateUserData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\DiscountConfig;
use App\Model\Entity\FormField;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\TripLocationPath;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
    private string $tax;

    public function __construct(
        #[Autowire('%app.tax%')]
        string $tax
    ) {
        $this->tax = $tax;
    }

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
                'html5'                       => true,
                'label'                       => 'form.admin.camp_date.deposit',
                'help'                        => $this->tax > 0.0 ? 'price_includes_tax' : null,
                'help_translation_parameters' => [
                    'tax' => $this->tax,
                ],
            ])
            ->add('isDepositUntilRelative', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.is_deposit_until_relative',
                'attr'     => [
                    'data-controller'                      => 'cv--checkbox',
                    'data-action'                          => 'cv--checkbox#updateVisibility',
                    'data-cv--checkbox-cv--content-outlet' => '.deposit-until-visibility',
                ],
            ])
            ->add('depositUntil', DateTimeType::class, [
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_date.deposit_until',
                'required' => false,
                'row_attr' => [
                    'class'                                   => 'mb-3 deposit-until-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '0',
                ],
                'label_attr' => [
                    'class' => 'required-conditional',
                ],
            ])
            ->add('depositUntilRelative', IntegerType::class, [
                'label'    => 'form.admin.camp_date.deposit_until_relative',
                'required' => false,
                'attr'     => [
                    'min' => 1,
                ],
                'row_attr' => [
                    'class'                                   => 'mb-3 deposit-until-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'label_attr' => [
                    'class' => 'required-conditional',
                ],
            ])
            ->add('priceWithoutDeposit', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5'                       => true,
                'label'                       => 'form.admin.camp_date.price_without_deposit',
                'help'                        => $this->tax > 0.0 ? 'price_includes_tax' : null,
                'help_translation_parameters' => [
                    'tax' => $this->tax,
                ],
            ])
            ->add('isPriceWithoutDepositUntilRelative', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.is_price_without_deposit_until_relative',
                'attr'     => [
                    'data-controller'                      => 'cv--checkbox',
                    'data-action'                          => 'cv--checkbox#updateVisibility',
                    'data-cv--checkbox-cv--content-outlet' => '.price-without-deposit-until-visibility',
                ],
            ])
            ->add('priceWithoutDepositUntil', DateTimeType::class, [
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_date.price_without_deposit_until',
                'required' => false,
                'row_attr' => [
                    'class'                                   => 'mb-3 price-without-deposit-until-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '0',
                ],
                'label_attr' => [
                    'class' => 'required-conditional',
                ],
            ])
            ->add('priceWithoutDepositUntilRelative', IntegerType::class, [
                'label'    => 'form.admin.camp_date.price_without_deposit_until_relative',
                'required' => false,
                'attr'     => [
                    'min' => 1,
                ],
                'row_attr' => [
                    'class'                                   => 'mb-3 price-without-deposit-until-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'label_attr' => [
                    'class' => 'required-conditional',
                ],
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
            ->add('isHidden', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.is_hidden',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.description',
            ])
            ->add('discountConfig', EntityType::class, [
                'class'        => DiscountConfig::class,
                'choice_label' => 'name',
                'choices'      => $options['choices_discount_configs'],
                'placeholder'  => 'form.common.choice.none.female',
                'required'     => false,
                'label'        => 'form.admin.camp_date.discount_config',
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
            ->add('campDateUsersData', CollectionType::class, [
                'entry_type'                  => CampDateUserType::class,
                'label'                       => 'form.admin.camp_date.users',
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
                'prototype_data' => new CampDateUserData(),
            ])
            ->add('addCampDateUserData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.camp_date.add_user',
                'collection_name' => 'campDateUsersData',
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
            'block_prefix'                => 'admin_camp_date',
            'choices_discount_configs'    => [],
            'choices_trip_location_paths' => [],
            'choices_form_fields'         => [],
            'choices_attachment_configs'  => [],
            'choices_purchasable_items'   => [],
        ]);

        $resolver->setAllowedTypes('choices_discount_configs', DiscountConfig::class . '[]');
        $resolver->setAllowedTypes('choices_trip_location_paths', TripLocationPath::class . '[]');
        $resolver->setAllowedTypes('choices_form_fields', FormField::class . '[]');
        $resolver->setAllowedTypes('choices_attachment_configs', AttachmentConfig::class . '[]');
        $resolver->setAllowedTypes('choices_purchasable_items', PurchasableItem::class . '[]');
    }
}