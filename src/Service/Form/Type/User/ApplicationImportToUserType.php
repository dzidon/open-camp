<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationImportToUserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationImportToUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var null|ApplicationImportToUserData $applicationImportToUserData */
            $applicationImportToUserData = $event->getData();
            $form = $event->getForm();

            if ($applicationImportToUserData === null || !$applicationImportToUserData->allowImportBillingData())
            {
                return;
            }

            $form
                ->add('skipBillingData', CheckboxType::class, [
                    'required' => false,
                    'label'    => 'form.user.application_import_to_user.skip_billing_data',
                    'priority' => 300,
                ])
            ;
        });

        $builder
            ->add('applicationImportToUserContactsData', CollectionType::class, [
                'entry_type' => ApplicationImportToUserContactType::class,
                'label'      => 'form.user.application_import_to_user.contacts',
                'priority'   => 200,
            ])
        ;

        $builder
            ->add('applicationImportToUserCampersData', CollectionType::class, [
                'entry_type' => ApplicationImportToUserCamperType::class,
                'label'      => 'form.user.application_import_to_user.campers',
                'priority'   => 100,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationImportToUserData::class,
            'block_prefix' => 'user_application_import',
        ]);
    }
}