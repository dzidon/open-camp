<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationCamperPurchasableItemsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationCamperPurchasableItemsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $instancesEmptyData = $options['instances_empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instancesEmptyData): void
        {
            /** @var ApplicationCamperPurchasableItemsData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $applicationCamper = $data->getApplicationCamper();

            if ($applicationCamper === null)
            {
                return;
            }

            $nameFull = $applicationCamper->getNameFull();

            $form
                ->add('applicationPurchasableItemsData', CollectionType::class, [
                    'entry_type'    => ApplicationPurchasableItemType::class,
                    'entry_options' => [
                        'instances_empty_data' => $instancesEmptyData,
                        'row_attr'             => [
                            'class' => 'm-0',
                        ],
                    ],
                    'row_attr' => [
                        'class' => 'm-0',
                    ],
                    'label'                        => 'form.common.application_camper_purchasable_items.purchasable_items',
                    'label_translation_parameters' => [
                        'camper' => $nameFull,
                    ],
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationCamperPurchasableItemsData::class,
            'block_prefix' => 'common_application_camper_purchasable_items',
            'label'        => false,
        ]);

        $resolver->setDefined('instances_empty_data');
        $resolver->setAllowedTypes('instances_empty_data', 'callable[]');
        $resolver->setRequired('instances_empty_data');
    }
}