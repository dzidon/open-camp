<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationData;
use App\Service\Form\Type\Common\ApplicationAttachmentType;
use App\Service\Form\Type\Common\ApplicationDiscountsType;
use App\Service\Form\Type\Common\ApplicationFormFieldValueType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application edit.
 */
class ApplicationType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ApplicationData $applicationData */
        $applicationData = $builder->getData();
        $application = $applicationData->getApplication();

        if ($this->security->isGranted('application_state_update') || $this->security->isGranted('guide_access_state', $application))
        {
            $builder
                ->add('isAccepted', ChoiceType::class, [
                    'choices'     => [
                        'application.is_accepted_state.admin.unsettled' => null,
                        'application.is_accepted_state.admin.accepted'  => true,
                        'application.is_accepted_state.admin.declined'  => false,
                    ],
                    'expanded' => true,
                    'required' => true,
                    'label'    => 'form.admin.application.is_accepted',
                    'priority' => 1400,
                ])
            ;
        }

        if ($this->security->isGranted('application_update') || $this->security->isGranted('guide_access_update', $application))
        {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
            {
                /** @var null|ApplicationData $applicationData */
                $applicationData = $event->getData();
                $form = $event->getForm();

                if ($applicationData === null)
                {
                    return;
                }

                $application = $applicationData->getApplication();

                if (!$application->isEuBusinessDataEnabled())
                {
                    return;
                }

                $form
                    ->add('isCompany', CheckboxType::class, [
                        'label' => 'form.admin.application.is_company',
                        'attr'  => [
                            'data-controller'                      => 'cv--checkbox',
                            'data-action'                          => 'cv--checkbox#updateVisibility',
                            'data-cv--checkbox-cv--content-outlet' => '.company-fields-visibility',
                        ],
                        'required' => false,
                        'priority' => 600,
                    ])
                    ->add('businessName', TextType::class, [
                        'label'    => 'form.admin.application.business_name',
                        'row_attr' => [
                            'class'                                   => 'company-fields-visibility',
                            'data-controller'                         => 'cv--content',
                            'data-cv--content-show-when-chosen-value' => '1',
                        ],
                        'required' => false,
                        'priority' => 500,
                    ])
                    ->add('businessCin', TextType::class, [
                        'label'      => 'form.admin.application.business_cin',
                        'label_attr' => [
                            'class' => 'required'
                        ],
                        'row_attr' => [
                            'class'                                   => 'company-fields-visibility',
                            'data-controller'                         => 'cv--content',
                            'data-cv--content-show-when-chosen-value' => '1',
                        ],
                        'required' => false,
                        'priority' => 400,
                    ])
                    ->add('businessVatId', TextType::class, [
                        'label'      => 'form.admin.application.business_vat_id',
                        'label_attr' => [
                            'class' => 'required'
                        ],
                        'row_attr' => [
                            'class'                                   => 'company-fields-visibility',
                            'data-controller'                         => 'cv--content',
                            'data-cv--content-show-when-chosen-value' => '1',
                        ],
                        'required' => false,
                        'priority' => 300,
                    ])
                ;
            });

            $builder
                ->add('email', EmailType::class, [
                    'attr' => [
                        'autofocus' => 'autofocus',
                    ],
                    'label'    => 'form.admin.application.email',
                    'priority' => 1300,
                ])
                ->add('nameFirst', TextType::class, [
                    'label'    => 'form.admin.application.name_first',
                    'priority' => 1200,
                ])
                ->add('nameLast', TextType::class, [
                    'label'    => 'form.admin.application.name_last',
                    'priority' => 1100,
                ])
                ->add('street', TextType::class, [
                    'label'    => 'form.admin.application.street',
                    'priority' => 1000,
                ])
                ->add('town', TextType::class, [
                    'label'    => 'form.admin.application.town',
                    'priority' => 900,
                ])
                ->add('zip', TextType::class, [
                    'label'    => 'form.admin.application.zip',
                    'priority' => 800,
                ])
                ->add('country', CountryType::class, [
                    'placeholder'      => 'form.common.choice.choose',
                    'placeholder_attr' => [
                        'disabled' => 'disabled'
                    ],
                    'label'    => 'form.admin.application.country',
                    'priority' => 700,
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
                    'priority' => 200,
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
                    'priority' => 100,
                ])
                ->add('applicationDiscountsData', ApplicationDiscountsType::class, [
                    'row_attr' => [
                        'class' => 'm-0',
                    ],
                    'label'    => false,
                    'priority' => 0,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => ApplicationData::class,
            'block_prefix'  => 'admin_application',
            'choices_roles' => [],
        ]);
    }
}