<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin purchasable item variant creation.
 */
class PurchasableItemVariantCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('purchasableItemVariantData', PurchasableItemVariantType::class, [
                'label' => false,
            ])
            ->add('purchasableItemVariantValuesData', CollectionType::class, [
                'entry_type'    => PurchasableItemVariantValueType::class,
                'label'         => 'form.admin.purchasable_item_variant_creation.purchasable_item_variant_values',
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'entry_options' => [
                    'label'         => false,
                    'remove_button' => true,
                ],
                'prototype_options' => [
                    'remove_button' => true,
                ],
                'prototype_data' => new PurchasableItemVariantValueData(),
            ])
            ->add('addPurchasableItemVariantValueData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.purchasable_item_variant_creation.add_purchasable_item_variant_value',
                'collection_name' => 'purchasableItemVariantValuesData',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchasableItemVariantCreationData::class,
        ]);
    }
}