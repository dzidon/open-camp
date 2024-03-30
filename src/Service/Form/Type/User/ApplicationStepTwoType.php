<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationStepTwoData;
use App\Model\Entity\PaymentMethod;
use App\Service\Form\Type\Common\ApplicationCamperPurchasableItemsType;
use App\Service\Form\Type\Common\ApplicationCustomerChannelType;
use App\Service\Form\Type\Common\ApplicationDiscountsType;
use App\Service\Form\Type\Common\ApplicationPurchasableItemType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationStepTwoType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $instancesEmptyData = $options['instances_empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instancesEmptyData): void
        {
            /** @var ApplicationStepTwoData $data */
            $data = $event->getData();
            $form = $event->getForm();

            $this->addCamperApplicationPurchasableItemsData($data, $form, $instancesEmptyData);
            $this->addApplicationPurchasableItemsData($data, $form, $instancesEmptyData);
        });

        $builder
            ->add('paymentMethod', EntityType::class, [
                'class'        => PaymentMethod::class,
                'choice_label' => function (PaymentMethod $paymentMethod): string
                {
                    $label = $paymentMethod->getLabel();

                    return $this->translator->trans($label);
                },
                'expanded'         => true,
                'choices'          => $options['choices_payment_methods'],
                'label'            => 'form.user.application_step_two.payment_method',
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'priority' => 600,
            ])
            ->add('applicationDiscountsData', ApplicationDiscountsType::class, [
                'row_attr' => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 500,
            ])
            ->add('customerChannel', ApplicationCustomerChannelType::class, [
                'required'    => false,
                'placeholder' => 'application_customer_channel.none',
                'label'       => 'form.user.application_step_two.customer_channel',
                'attr'        => [
                    'data-controller'                         => 'cv--other-input',
                    'data-action'                             => 'cv--other-input#updateVisibility',
                    'data-cv--other-input-cv--content-outlet' => '.channel-other-field-visibility',
                ],
                'priority' => 200,
            ])
            ->add('customerChannelOther', TextType::class, [
                'required'   => false,
                'label'      => 'form.user.application_step_two.customer_channel_other',
                'label_attr' => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'class'                                   => 'channel-other-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'priority' => 100,
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.application_step_two.note',
                'priority' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => ApplicationStepTwoData::class,
            'block_prefix'            => 'user_application_step_two',
            'choices_payment_methods' => [],
        ]);

        $resolver->setAllowedTypes('choices_payment_methods', ['array']);

        $resolver->setDefined('instances_empty_data');
        $resolver->setAllowedTypes('instances_empty_data', 'callable[]');
        $resolver->setRequired('instances_empty_data');
    }

    private function addCamperApplicationPurchasableItemsData(ApplicationStepTwoData $data,
                                                              FormInterface          $form,
                                                              array                  $instancesEmptyData): void
    {
        $applicationCamperPurchasableItemsData = $data->getApplicationCamperPurchasableItemsData();

        if (empty($applicationCamperPurchasableItemsData))
        {
            return;
        }

        $form
            ->add('applicationCamperPurchasableItemsData', CollectionType::class, [
                'entry_type'    => ApplicationCamperPurchasableItemsType::class,
                'label'         => false,
                'entry_options' => [
                    'instances_empty_data' => $instancesEmptyData,
                    'row_attr'             => [
                        'class' => 'mb-0',
                    ],
                ],
                'priority' => 400,
            ])
        ;
    }

    private function addApplicationPurchasableItemsData(ApplicationStepTwoData $data,
                                                        FormInterface          $form,
                                                        array                  $instancesEmptyData): void
    {
        $applicationPurchasableItemsData = $data->getApplicationPurchasableItemsData();

        if (empty($applicationPurchasableItemsData))
        {
            return;
        }

        $label = 'form.user.application_step_two.purchasable_items';
        $applicationCamperPurchasableItemsData = $data->getApplicationCamperPurchasableItemsData();

        if (!empty($applicationCamperPurchasableItemsData))
        {
            $label = 'form.user.application_step_two.purchasable_items_global';
        }

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
                'label'    => $label,
                'priority' => 300,
            ])
        ;
    }
}