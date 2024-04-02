<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationCamperPurchasableItemsData;
use App\Library\Data\Admin\ApplicationPurchasableItemData;
use App\Library\Data\Admin\ApplicationPurchasableItemsData;
use App\Service\Form\Type\Common\ApplicationCamperPurchasableItemsType;
use App\Service\Form\Type\Common\ApplicationPurchasableItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationPurchasableItemsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $instancesEmptyData = $options['instances_empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instancesEmptyData): void
        {
            /** @var ApplicationPurchasableItemsData $data */
            $data = $event->getData();
            $form = $event->getForm();

            if (!empty($data->getApplicationCamperPurchasableItemsData()))
            {
                $form
                    ->add('applicationCamperPurchasableItemsData', CollectionType::class, [
                        'entry_type'    => ApplicationCamperPurchasableItemsType::class,
                        'label'         => false,
                        'entry_options' => [
                            'data_class'           => ApplicationCamperPurchasableItemsData::class,
                            'instances_empty_data' => $instancesEmptyData,
                            'row_attr'             => [
                                'class' => 'mb-0',
                            ],
                        ],
                        'priority' => 200,
                    ])
                ;
            }

            if (!empty($data->getApplicationPurchasableItemsData()))
            {
                $label = 'form.admin.application_purchasable_items.label_individual';
                $applicationCamperPurchasableItemsData = $data->getApplicationCamperPurchasableItemsData();

                if (!empty($applicationCamperPurchasableItemsData))
                {
                    $label = 'form.admin.application_purchasable_items.label_global';
                }

                $form
                    ->add('applicationPurchasableItemsData', CollectionType::class, [
                        'entry_type'    => ApplicationPurchasableItemType::class,
                        'entry_options' => [
                            'data_class'           => ApplicationPurchasableItemData::class,
                            'instances_empty_data' => $instancesEmptyData,
                            'row_attr'             => [
                                'class' => 'm-0',
                            ],
                        ],
                        'row_attr' => [
                            'class' => 'm-0',
                        ],
                        'label'    => $label,
                        'priority' => 100,
                    ])
                ;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationPurchasableItemsData::class,
            'block_prefix' => 'admin_application_purchasable_items',
        ]);

        $resolver->setDefined('instances_empty_data');
        $resolver->setAllowedTypes('instances_empty_data', 'callable[]');
        $resolver->setRequired('instances_empty_data');
    }
}