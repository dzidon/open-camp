<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\DiscountConfigData;
use App\Library\Data\Admin\DiscountRecurringCamperConfigData;
use App\Library\Data\Admin\DiscountSiblingConfigData;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin discount config editing.
 */
class DiscountConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.discount_config.name',
                'help'  => 'form.admin.help.value_visible_for_admins_only',
            ])
            ->add('discountRecurringCamperConfigsData', CollectionType::class, [
                'entry_type'                  => DiscountRecurringCamperConfigType::class,
                'label'                       => 'form.admin.discount_config.discount_recurring_camper_configs',
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
                'prototype_data' => new DiscountRecurringCamperConfigData(),
            ])
            ->add('addDiscountRecurringCamperConfigData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.discount_config.add_discount_recurring_camper_config',
                'collection_name' => 'discountRecurringCamperConfigsData',
            ])
            ->add('discountSiblingConfigsData', CollectionType::class, [
                'entry_type'                  => DiscountSiblingConfigType::class,
                'label'                       => 'form.admin.discount_config.discount_sibling_configs',
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
                'prototype_data' => new DiscountSiblingConfigData(),
            ])
            ->add('addDiscountSiblingConfigData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.discount_config.add_discount_sibling_config',
                'collection_name' => 'discountSiblingConfigsData',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => DiscountConfigData::class,
            'block_prefix' => 'admin_discount_config',
        ]);
    }
}