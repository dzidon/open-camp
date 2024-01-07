<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Library\Data\User\ContactData;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Step one of the camp date application.
 */
class ApplicationStepOneType extends AbstractType
{
    private bool $isEuBusinessDataEnabled;

    public function __construct(bool $isEuBusinessDataEnabled)
    {
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ApplicationCamperData $defaultApplicationCamperData */
        $defaultApplicationCamperData = $options['application_camper_default_data'];

        /** @var ContactData $defaultContactData */
        $defaultContactData = $options['contact_default_data'];

        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label'    => 'form.user.application_step_one.email.label',
                'help'     => 'form.user.application_step_one.email.help',
                'priority' => 5000,
            ])
            ->add('nameFirst', TextType::class, [
                'label'    => 'form.user.application_step_one.name_first',
                'priority' => 4900,
            ])
            ->add('nameLast', TextType::class, [
                'label'    => 'form.user.application_step_one.name_last',
                'priority' => 4800,
            ])
            ->add('street', TextType::class, [
                'label'    => 'form.user.application_step_one.street',
                'priority' => 4700,
            ])
            ->add('town', TextType::class, [
                'label'    => 'form.user.application_step_one.town',
                'priority' => 4600,
            ])
            ->add('zip', TextType::class, [
                'label'    => 'form.user.application_step_one.zip',
                'priority' => 4500,
            ])
            ->add('country', CountryType::class, [
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'label'    => 'form.user.application_step_one.country',
                'priority' => 4400,
            ])
        ;

        if ($this->isEuBusinessDataEnabled)
        {
            $builder
                ->add('isCompany', CheckboxType::class, [
                    'label'  => 'form.user.application_step_one.is_company',
                    'attr'   => [
                        'data-controller'                      => 'cv--checkbox',
                        'data-action'                          => 'cv--checkbox#updateVisibility',
                        'data-cv--checkbox-cv--content-outlet' => '.company-fields-visibility',
                    ],
                    'required' => false,
                    'priority' => 4300,
                ])
                ->add('businessName', TextType::class, [
                    'label'    => 'form.user.application_step_one.business_name',
                    'row_attr' => [
                        'class'                                    => 'company-fields-visibility',
                        'data-controller'                          => 'cv--content',
                        'data-cv--content-show-when-checked-value' => '1',
                    ],
                    'required' => false,
                    'priority' => 4200,
                ])
                ->add('businessCin', TextType::class, [
                    'label'      => 'form.user.application_step_one.business_cin',
                    'label_attr' => [
                        'class' => 'required'
                    ],
                    'row_attr' => [
                        'class'                                    => 'company-fields-visibility',
                        'data-controller'                          => 'cv--content',
                        'data-cv--content-show-when-checked-value' => '1',
                    ],
                    'required' => false,
                    'priority' => 4100,
                ])
                ->add('businessVatId', TextType::class, [
                    'label'      => 'form.user.application_step_one.business_vat_id',
                    'label_attr' => [
                        'class' => 'required'
                    ],
                    'row_attr' => [
                        'class'                                    => 'company-fields-visibility',
                        'data-controller'                          => 'cv--content',
                        'data-cv--content-show-when-checked-value' => '1',
                    ],
                    'required' => false,
                    'priority' => 4000,
                ])
            ;
        }

        $builder
            ->add('applicationFormFieldValuesData', CollectionType::class, [
                'entry_type' => ApplicationFormFieldValueType::class,
                'row_attr'   => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 3900,
            ])
            ->add('applicationAttachmentsData', CollectionType::class, [
                'entry_type' => ApplicationAttachmentType::class,
                'row_attr'   => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 3800,
            ])
            ->add('contactsData', CollectionType::class, [
                'entry_type'    => ContactType::class,
                'label'         => 'form.user.application_step_one.contacts',
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_options' => [
                    'label'               => false,
                    'remove_button_label' => 'form.user.application_contact.remove_button',
                    'remove_button'       => true,
                    'empty_data'          => $defaultContactData,
                ],
                'prototype_options' => [
                    'remove_button_label' => 'form.user.application_contact.remove_button',
                    'remove_button'       => true,
                ],
                'prototype_data' => $defaultContactData,
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
                    'label'               => false,
                    'remove_button_label' => 'form.user.application_camper.remove_button',
                    'remove_button'       => true,
                    'empty_data'          => $defaultApplicationCamperData,
                ],
                'prototype_options' => [
                    'remove_button_label' => 'form.user.application_camper.remove_button',
                    'remove_button'       => true,
                ],
                'prototype_data' => $defaultApplicationCamperData,
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
            'data_class'   => ApplicationStepOneData::class,
            'block_prefix' => 'user_application_step_one',
        ]);

        $resolver->setDefined('application_camper_default_data');
        $resolver->setAllowedTypes('application_camper_default_data', ApplicationCamperData::class);
        $resolver->setRequired('application_camper_default_data');

        $resolver->setDefined('contact_default_data');
        $resolver->setAllowedTypes('contact_default_data', ContactData::class);
        $resolver->setRequired('contact_default_data');
    }
}