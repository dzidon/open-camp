<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Camper;
use App\Model\Entity\Contact;
use App\Service\Form\Type\Common\ApplicationAttachmentType;
use App\Service\Form\Type\Common\ApplicationCamperType;
use App\Service\Form\Type\Common\ApplicationFormFieldValueType;
use App\Service\Form\Type\Common\BillingType;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use App\Service\Form\Type\Common\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Step one of the camp date application.
 */
class ApplicationStepOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var callable $emptyApplicationCamperData */
        $emptyApplicationCamperData = $options['application_camper_empty_data'];

        /** @var callable $contactEmptyData */
        $contactEmptyData = $options['contact_empty_data'];

        /** @var Contact[] $loadableContacts */
        $loadableContacts = $options['loadable_contacts'];

        /** @var Camper[] $loadableCampers */
        $loadableCampers = $options['loadable_campers'];

        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autofocus'                         => 'autofocus',
                    'data-app--contact-autofill-target' => 'email',
                    'data-action'                       => 'app--contact-autofill#fillFirstContact'
                ],
                'label'    => 'form.user.application_step_one.email.label',
                'help'     => 'form.user.application_step_one.email.help',
                'priority' => 4100,
            ])
            ->add('billingData', BillingType::class, [
                'row_attr' => [
                    'class' => 'm-0',
                ],
                'enable_contact_autofill' => true,
                'label'                   => false,
                'priority'                => 4000,
            ])
            ->add('applicationFormFieldValuesData', CollectionType::class, [
                'entry_type' => ApplicationFormFieldValueType::class,
                'entry_options' => [
                    'row_attr'  => [
                        'class' => 'm-0',
                    ],
                ],
                'row_attr'   => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 3900,
            ])
            ->add('applicationAttachmentsData', CollectionType::class, [
                'entry_type'    => ApplicationAttachmentType::class,
                'entry_options' => [
                    'row_attr'  => [
                        'class' => 'm-0',
                    ],
                ],
                'row_attr'  => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 3800,
            ])
            ->add('contactsData', CollectionType::class, [
                'entry_type'   => ContactType::class,
                'label'        => 'form.user.application_step_one.contacts',
                'allow_add'    => true,
                'allow_delete' => true,
                'attr'         => [
                    'data-app--contact-autofill-target' => 'contacts',
                ],
                'entry_options' => [
                    'label'                  => false,
                    'remove_button_label'    => 'form.user.application_contact.remove_button',
                    'remove_button'          => true,
                    'enable_contact_loading' => true,
                    'empty_data'             => $contactEmptyData,
                    'loadable_contacts'      => $loadableContacts,
                ],
                'prototype_options' => [
                    'remove_button_label'    => 'form.user.application_contact.remove_button',
                    'remove_button'          => true,
                    'enable_contact_loading' => true,
                    'loadable_contacts'      => $loadableContacts,
                ],
                'prototype_data' => $contactEmptyData(),
                'priority'       => 3700,
            ])
            ->add('addContactData', CollectionAddItemButtonType::class, [
                'label'           => 'form.user.application_step_one.add_contact',
                'collection_name' => 'contactsData',
                'priority'        => 3600,
            ])
            ->add('applicationCampersData', CollectionType::class, [
                'entry_type'    => ApplicationCamperType::class,
                'label'         => 'form.user.application_step_one.campers',
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_options' => [
                    'data_class'            => ApplicationCamperData::class,
                    'label'                 => false,
                    'remove_button_label'   => 'form.common.application_camper.remove_button',
                    'remove_button'         => true,
                    'enable_camper_loading' => true,
                    'empty_data'            => $emptyApplicationCamperData,
                    'loadable_campers'      => $loadableCampers,
                ],
                'prototype_options' => [
                    'data_class'            => ApplicationCamperData::class,
                    'remove_button_label'   => 'form.common.application_camper.remove_button',
                    'remove_button'         => true,
                    'enable_camper_loading' => true,
                    'loadable_campers'      => $loadableCampers,
                ],
                'prototype_data' => $emptyApplicationCamperData(),
                'priority'       => 3500,
            ])
            ->add('addCamperData', CollectionAddItemButtonType::class, [
                'label'           => 'form.user.application_step_one.add_camper',
                'collection_name' => 'applicationCampersData',
                'priority'        => 3400,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => ApplicationStepOneData::class,
            'block_prefix'      => 'user_application_step_one',
            'loadable_contacts' => [],
            'loadable_campers'  => [],
            'attr'              => [
                'data-controller' => 'app--contact-autofill',
            ],
        ]);

        $resolver->setAllowedTypes('loadable_contacts', Contact::class . '[]');
        $resolver->setAllowedTypes('loadable_campers', Camper::class . '[]');

        $resolver->setDefined('application_camper_empty_data');
        $resolver->setAllowedTypes('application_camper_empty_data', 'callable');
        $resolver->setRequired('application_camper_empty_data');

        $resolver->setDefined('contact_empty_data');
        $resolver->setAllowedTypes('contact_empty_data', 'callable');
        $resolver->setRequired('contact_empty_data');
    }
}