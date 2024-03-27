<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationPurchasableItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationPurchasableItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var callable[] $instancesEmptyData */
        $instancesEmptyData = $options['instances_empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instancesEmptyData): void
        {
            /** @var ApplicationPurchasableItemData $applicationPurchasableItemData */
            $applicationPurchasableItemData = $event->getData();

            if ($applicationPurchasableItemData === null)
            {
                return;
            }

            $form = $event->getForm();
            $applicationPurchasableItem = $applicationPurchasableItemData->getApplicationPurchasableItem();
            $idString = $applicationPurchasableItem
                ->getId()
                ->toRfc4122()
            ;

            if (!array_key_exists($idString, $instancesEmptyData))
            {
                return;
            }

            $emptyInstanceData = $instancesEmptyData[$idString];
            $hasMultipleVariants = $applicationPurchasableItem->hasMultipleVariants();
            $application = $applicationPurchasableItem->getApplication();
            $isIndividualMode = $application->isPurchasableItemsIndividualMode();
            $maxAmount = $applicationPurchasableItem->getMaxAmount();

            if (!$isIndividualMode)
            {
                $maxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
            }

            $canAddAndRemove = $hasMultipleVariants && $maxAmount > 1;
            $label = $applicationPurchasableItem->getLabel();

            $form
                ->add('applicationPurchasableItemInstancesData', CollectionType::class, [
                    'entry_type'                  => ApplicationPurchasableItemInstanceType::class,
                    'label'                       => $label,
                    'suppress_required_rendering' => true,
                    'allow_add'                   => $canAddAndRemove,
                    'allow_delete'                => $canAddAndRemove,
                    'entry_options'               => [
                        'label'               => false,
                        'remove_button_label' => 'form.user.application_purchasable_item_instance.remove_button',
                        'remove_button'       => $canAddAndRemove,
                        'empty_data'          => $emptyInstanceData,
                    ],
                    'prototype_options' => [
                        'remove_button_label' => 'form.user.application_purchasable_item_instance.remove_button',
                        'remove_button'       => true,
                    ],
                    'prototype'      => $canAddAndRemove,
                    'prototype_data' => $emptyInstanceData(),
                ])
            ;

            if ($canAddAndRemove)
            {
                $form
                    ->add('addApplicationPurchasableItemInstanceData', CollectionAddItemButtonType::class, [
                        'label'           => 'form.user.application_purchasable_item.add_instance',
                        'collection_name' => 'applicationPurchasableItemInstancesData',
                    ])
                ;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationPurchasableItemData::class,
            'block_prefix' => 'common_application_purchasable_item',
            'label'        => false,
        ]);

        $resolver->setDefined('instances_empty_data');
        $resolver->setAllowedTypes('instances_empty_data', 'callable[]');
        $resolver->setRequired('instances_empty_data');
    }
}