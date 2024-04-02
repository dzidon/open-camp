<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationData;
use App\Service\Form\Type\Common\ApplicationAttachmentType;
use App\Service\Form\Type\Common\ApplicationCustomerChannelType;
use App\Service\Form\Type\Common\ApplicationDiscountsType;
use App\Service\Form\Type\Common\ApplicationFormFieldValueType;
use App\Service\Form\Type\Common\BillingType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
                    'priority' => 800,
                ])
            ;
        }

        if ($this->security->isGranted('application_update') || $this->security->isGranted('guide_access_update', $application))
        {
            $builder
                ->add('email', EmailType::class, [
                    'attr' => [
                        'autofocus' => 'autofocus',
                    ],
                    'label'    => 'form.admin.application.email',
                    'priority' => 700,
                ])
                ->add('billingData', BillingType::class, [
                    'row_attr' => [
                        'class' => 'm-0',
                    ],
                    'label'    => false,
                    'priority' => 600,
                ])
                ->add('customerChannel', ApplicationCustomerChannelType::class, [
                    'required'    => false,
                    'placeholder' => 'application_customer_channel.none',
                    'label'       => 'form.admin.application.customer_channel',
                    'attr'        => [
                        'data-controller'                         => 'cv--other-input',
                        'data-action'                             => 'cv--other-input#updateVisibility',
                        'data-cv--other-input-cv--content-outlet' => '.channel-other-field-visibility',
                    ],
                    'priority' => 500,
                ])
                ->add('customerChannelOther', TextType::class, [
                    'required'   => false,
                    'label'      => 'form.admin.application.customer_channel_other',
                    'label_attr' => [
                        'class' => 'required'
                    ],
                    'row_attr' => [
                        'class'                                   => 'channel-other-field-visibility',
                        'data-controller'                         => 'cv--content',
                        'data-cv--content-show-when-chosen-value' => '1',
                    ],
                    'priority' => 400,
                ])
                ->add('note', TextareaType::class, [
                    'required' => false,
                    'label'    => 'form.admin.application.note',
                    'priority' => 300,
                ])
                ->add('applicationFormFieldValuesData', CollectionType::class, [
                    'entry_type' => ApplicationFormFieldValueType::class,
                    'entry_options' => [
                        'row_attr'  => [
                            'class' => 'm-0',
                        ],
                    ],
                    'row_attr' => [
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