<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Library\Data\User\ApplicationStepTwoUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationStepTwoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('applicationPurchasableItemsData', CollectionType::class, [
                'entry_type'    => ApplicationPurchasableItemType::class,
                'entry_options' => [
                    'instance_defaults_data' => $options['instance_defaults_data'],
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationStepTwoUpdateData::class,
            'block_prefix' => 'user_application_step_two',
            'label'        => false,
        ]);

        $resolver->setDefined('instance_defaults_data');
        $resolver->setAllowedTypes('instance_defaults_data', ApplicationPurchasableItemInstanceData::class . '[]');
        $resolver->setRequired('instance_defaults_data');
    }
}