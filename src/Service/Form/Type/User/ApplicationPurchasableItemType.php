<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationPurchasableItemData;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationPurchasableItemType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ApplicationPurchasableItemInstanceData[] $instanceDefaultsData */
        $instanceDefaultsData = $options['instance_defaults_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instanceDefaultsData): void
        {
            /** @var ApplicationPurchasableItemData $applicationPurchasableItemData */
            $applicationPurchasableItemData = $event->getData();

            if ($applicationPurchasableItemData === null)
            {
                return;
            }

            $form = $event->getForm();
            $config = $form->getConfig();
            $index = $config->getName();

            if (!array_key_exists($index, $instanceDefaultsData))
            {
                return;
            }

            $defaultInstanceData = $instanceDefaultsData[$index];
            $applicationPurchasableItem = $applicationPurchasableItemData->getApplicationPurchasableItem();
            $hasMultipleVariants = $applicationPurchasableItem->hasMultipleVariants();
            $isCalculatedMaxAmountGreaterThanOne = $applicationPurchasableItem->isCalculatedMaxAmountGreaterThanOne();
            $canAddAndRemove = $hasMultipleVariants && $isCalculatedMaxAmountGreaterThanOne;
            $removeButtonLabel = $this->translator->trans('form.user.application_purchasable_item_instance.remove_button');

            $form
                ->add('applicationPurchasableItemInstancesData', CollectionType::class, [
                    'entry_type'                  => ApplicationPurchasableItemInstanceType::class,
                    'label'                       => $applicationPurchasableItem->getLabel(),
                    'suppress_required_rendering' => true,
                    'translation_domain'          => false,
                    'allow_add'                   => $canAddAndRemove,
                    'allow_delete'                => $canAddAndRemove,
                    'entry_options'               => [
                        'label'               => false,
                        'remove_button_label' => $removeButtonLabel,
                        'remove_button'       => $canAddAndRemove,
                        'empty_data'          => $defaultInstanceData,
                    ],
                    'prototype_options' => [
                        'remove_button_label' => $removeButtonLabel,
                        'remove_button'       => true,
                    ],
                    'prototype'      => $canAddAndRemove,
                    'prototype_data' => $defaultInstanceData,
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
            'data_class' => ApplicationPurchasableItemData::class,
            'label'      => false,
            'row_attr'   => [
                'class' => 'mb-0'
            ],
        ]);

        $resolver->setDefined('instance_defaults_data');
        $resolver->setAllowedTypes('instance_defaults_data', ApplicationPurchasableItemInstanceData::class . '[]');
        $resolver->setRequired('instance_defaults_data');
    }
}