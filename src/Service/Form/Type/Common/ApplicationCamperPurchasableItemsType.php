<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationCamperPurchasableItemsData;
use App\Library\Data\Common\ApplicationPurchasableItemInstanceData;
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
        $instanceDefaultsData = $options['instance_defaults_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instanceDefaultsData): void
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
                        'instance_defaults_data' => $instanceDefaultsData,
                        'row_attr'               => [
                            'class' => 'm-0',
                        ],
                    ],
                    'row_attr' => [
                        'class' => 'm-0',
                    ],
                    'label'                        => 'form.user.application_camper_purchasable_items.purchasable_items',
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

        $resolver->setDefined('instance_defaults_data');
        $resolver->setAllowedTypes('instance_defaults_data', ApplicationPurchasableItemInstanceData::class . '[]');
        $resolver->setRequired('instance_defaults_data');
    }
}