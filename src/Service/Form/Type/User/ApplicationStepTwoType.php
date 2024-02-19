<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Library\Data\User\ApplicationStepTwoUpdateData;
use App\Model\Entity\PaymentMethod;
use App\Service\Form\Type\Common\ApplicationCustomerChannelType;
use NumberFormatter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Traversable;

class ApplicationStepTwoType extends AbstractType implements DataMapperInterface
{
    private TranslatorInterface $translator;

    private RequestStack $requestStack;

    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $instanceDefaultsData = $options['instance_defaults_data'];

        $builder->setDataMapper($this);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($instanceDefaultsData): void
        {
            /** @var ApplicationStepTwoUpdateData $data */
            $data = $event->getData();
            $form = $event->getForm();

            $this->addDiscountSiblingsField($data, $form);
            $this->addCamperApplicationPurchasableItemsData($data, $form, $instanceDefaultsData);
            $this->addApplicationPurchasableItemsData($data, $form, $instanceDefaultsData);
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
            'data_class'              => ApplicationStepTwoUpdateData::class,
            'block_prefix'            => 'user_application_step_two',
            'choices_payment_methods' => [],
        ]);

        $resolver->setAllowedTypes('choices_payment_methods', ['array']);

        $resolver->setDefined('instance_defaults_data');
        $resolver->setAllowedTypes('instance_defaults_data', ApplicationPurchasableItemInstanceData::class . '[]');
        $resolver->setRequired('instance_defaults_data');
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if ($viewData === null)
        {
            return;
        }

        if (!$viewData instanceof ApplicationStepTwoUpdateData)
        {
            throw new UnexpectedTypeException($viewData, ApplicationStepTwoUpdateData::class);
        }

        /** @var ApplicationStepTwoUpdateData $applicationStepTwoUpdateData */
        /** @var FormInterface[] $forms */
        $applicationStepTwoUpdateData = $viewData;
        $forms = iterator_to_array($forms);

        if (array_key_exists('paymentMethod', $forms))
        {
            $forms['paymentMethod']->setData($applicationStepTwoUpdateData->getPaymentMethod());
        }

        if (array_key_exists('note', $forms))
        {
            $forms['note']->setData($applicationStepTwoUpdateData->getNote());
        }

        if (array_key_exists('customerChannel', $forms))
        {
            $forms['customerChannel']->setData($applicationStepTwoUpdateData->getCustomerChannel());
        }

        if (array_key_exists('customerChannelOther', $forms))
        {
            $forms['customerChannelOther']->setData($applicationStepTwoUpdateData->getCustomerChannelOther());
        }

        if (array_key_exists('applicationCamperPurchasableItemsData', $forms))
        {
            $forms['applicationCamperPurchasableItemsData']->setData($applicationStepTwoUpdateData->getApplicationCamperPurchasableItemsData());
        }

        if (array_key_exists('applicationPurchasableItemsData', $forms))
        {
            $forms['applicationPurchasableItemsData']->setData($applicationStepTwoUpdateData->getApplicationPurchasableItemsData());
        }

        if (array_key_exists('discountSiblingsInterval', $forms))
        {
            $discountSiblingsInterval = json_encode($applicationStepTwoUpdateData->getDiscountSiblingsInterval());
            $forms['discountSiblingsInterval']->setData($discountSiblingsInterval);
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var ApplicationStepTwoUpdateData $applicationStepTwoUpdateData */
        /** @var FormInterface[] $forms */
        $applicationStepTwoUpdateData = $viewData;
        $forms = iterator_to_array($forms);

        if (array_key_exists('paymentMethod', $forms))
        {
            $applicationStepTwoUpdateData->setPaymentMethod($forms['paymentMethod']->getData());
        }

        if (array_key_exists('note', $forms))
        {
            $applicationStepTwoUpdateData->setNote($forms['note']->getData());
        }

        if (array_key_exists('customerChannel', $forms))
        {
            $applicationStepTwoUpdateData->setCustomerChannel($forms['customerChannel']->getData());
        }

        if (array_key_exists('customerChannelOther', $forms))
        {
            $applicationStepTwoUpdateData->setCustomerChannelOther($forms['customerChannelOther']->getData());
        }

        if (array_key_exists('applicationPurchasableItemsData', $forms))
        {
            foreach ($forms['applicationPurchasableItemsData']->getData() as $applicationPurchasableItemsDatum)
            {
                $applicationStepTwoUpdateData->addApplicationPurchasableItemsDatum($applicationPurchasableItemsDatum);
            }
        }

        if (array_key_exists('applicationCamperPurchasableItemsData', $forms))
        {
            foreach ($forms['applicationCamperPurchasableItemsData']->getData() as $applicationCamperPurchasableItemsDatum)
            {
                $applicationStepTwoUpdateData->addApplicationCamperPurchasableItemsDatum($applicationCamperPurchasableItemsDatum);
            }
        }

        if (array_key_exists('discountSiblingsInterval', $forms))
        {
            $formDiscountSiblingsInterval = $forms['discountSiblingsInterval']->getData();
            $discountSiblingsInterval = json_decode($formDiscountSiblingsInterval, true);
            $applicationStepTwoUpdateData->setDiscountSiblingsInterval($discountSiblingsInterval);
        }
    }

    private function addCamperApplicationPurchasableItemsData(ApplicationStepTwoUpdateData $data,
                                                              FormInterface                $form,
                                                              array                        $instanceDefaultsData): void
    {
        $applicationCamperPurchasableItemsData = $data->getApplicationCamperPurchasableItemsData();

        if (empty($applicationCamperPurchasableItemsData))
        {
            return;
        }

        $form
            ->add('applicationCamperPurchasableItemsData', CollectionType::class, [
                'entry_type'    => ApplicationCamperPurchasableItemsType::class,
                'entry_options' => [
                    'instance_defaults_data' => $instanceDefaultsData,
                ],
                'label'    => false,
                'priority' => 400,
            ])
        ;
    }

    private function addApplicationPurchasableItemsData(ApplicationStepTwoUpdateData $data,
                                                        FormInterface                $form,
                                                        array                        $instanceDefaultsData): void
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
                    'instance_defaults_data' => $instanceDefaultsData,
                ],
                'label'    => $label,
                'priority' => 300,
            ])
        ;
    }

    private function addDiscountSiblingsField(ApplicationStepTwoUpdateData $data, FormInterface $form): void
    {
        $siblingsConfig = $data->getDiscountSiblingsConfig();

        if (empty($siblingsConfig))
        {
            return;
        }

        $nullOptionLabel = $this->translator->trans('form.user.application_step_two.discount_siblings.null_option');

        $choices = [
            $nullOptionLabel => json_encode(false),
        ];

        $request = $this->requestStack->getCurrentRequest();
        $fmt = numfmt_create($request->getLocale(), NumberFormatter::CURRENCY);
        $currency = $data->getCurrency();
        $numberOfApplicationCampers = $data->getNumberOfApplicationCampers();

        foreach ($siblingsConfig as $options)
        {
            $from = $options['from'];
            $to = $options['to'];
            $discount = $options['discount'];

            if (!$this->isSiblingDiscountIntervalEligibleForNumberOfCampers($from, $to, $numberOfApplicationCampers))
            {
                break;
            }

            if ($from === $to)
            {
                $message = $this->translator->trans('application.discount.siblings_single_number', [
                    'number' => $to,
                ]);
            }
            else if ($from === null)
            {
                $message = $this->translator->trans('application.discount.siblings_interval_from_null', [
                    'number_to'   => $to,
                ]);
            }
            else if ($to === null)
            {
                $message = $this->translator->trans('application.discount.siblings_interval_to_null', [
                    'number_from' => $from,
                ]);
            }
            else
            {
                $message = $this->translator->trans('application.discount.siblings_interval', [
                    'number_from' => $from,
                    'number_to'   => $to,
                ]);
            }

            $totalSiblingsDiscount = $numberOfApplicationCampers * $discount;
            $discountString = numfmt_format_currency($fmt, $totalSiblingsDiscount, $currency);

            $label = sprintf('%s (%s)', $message, $discountString);
            $value = json_encode(['from' => $from, 'to' => $to]);
            $choices[$label] = $value;
        }

        if (count($choices) <= 1)
        {
            return;
        }

        $form
            ->add('discountSiblingsInterval', ChoiceType::class, [
                'choices'                   => $choices,
                'expanded'                  => true,
                'label'                     => 'form.user.application_step_two.discount_siblings.label',
                'choice_translation_domain' => false,
                'priority'                  => 500,
            ])
        ;
    }

    private function isSiblingDiscountIntervalEligibleForNumberOfCampers(?int $discountSiblingsIntervalFrom,
                                                                         ?int $discountSiblingsIntervalTo,
                                                                         int  $numberOfApplicationCampers): bool
    {
        if ($discountSiblingsIntervalTo !== null && $numberOfApplicationCampers > $discountSiblingsIntervalTo)
        {
            return true;
        }

        return $discountSiblingsIntervalFrom === null || $numberOfApplicationCampers >= $discountSiblingsIntervalFrom;
    }
}