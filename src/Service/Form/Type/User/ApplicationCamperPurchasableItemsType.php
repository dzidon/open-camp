<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationCamperPurchasableItemsData;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
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
                    ],
                    'label'                        => 'form.user.application_step_two.purchasable_items_camper',
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
            'block_prefix' => 'user_application_camper_purchasable_item',
            'label'        => false,
            'row_attr' => [
                'class' => 'mb-0',
            ]
        ]);

        $resolver->setDefined('instance_defaults_data');
        $resolver->setAllowedTypes('instance_defaults_data', ApplicationPurchasableItemInstanceData::class . '[]');
        $resolver->setRequired('instance_defaults_data');
    }
}