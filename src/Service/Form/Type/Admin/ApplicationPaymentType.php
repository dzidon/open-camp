<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationPaymentData;
use App\Model\Entity\Application;
use App\Service\Form\Type\Common\ApplicationPaymentTypeType;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application payment edit.
 */
class ApplicationPaymentType extends AbstractType
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var null|Application $application */
        $application = $options['application'];
        $helpAmountMessage = null;
        $helpAmountMessageParameters = [];

        if ($application !== null)
        {
            $request = $this->requestStack->getCurrentRequest();
            $locale = $request->getLocale();
            $fmt = numfmt_create($locale, NumberFormatter::CURRENCY);

            $currency = $application->getCurrency();
            $helpAmountDeposit = $application->getFullDepositCached();
            $helpAmountRest = $application->getFullRestCached();
            $helpAmountFull = $application->getFullPriceCached();

            $helpAmountMessage = 'form.admin.application_payment.amount.help';
            $helpAmountMessageParameters = [
                'deposit' => numfmt_format_currency($fmt, $helpAmountDeposit, $currency),
                'rest'    => numfmt_format_currency($fmt, $helpAmountRest, $currency),
                'full'    => numfmt_format_currency($fmt, $helpAmountFull, $currency),
            ];
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void
            {
                /** @var ApplicationPaymentData $data */
                $data = $event->getData();
                $choices = $data->getValidStates();
                $form = $event->getForm();

                if (empty($choices))
                {
                    return;
                }

                $form
                    ->add('state', ChoiceType::class, [
                        'placeholder'      => 'form.common.choice.choose',
                        'placeholder_attr' => [
                            'disabled' => 'disabled'
                        ],
                        'choice_label' => function (string $choice): string
                        {
                            $choice = mb_strtolower($choice);

                            return "payment_state.$choice";
                        },
                        'choices'  => $choices,
                        'label'    => 'form.admin.application_payment.state',
                        'priority' => 100,
                    ])
                ;
            }
        );

        $builder
            ->add('amount', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5'                       => true,
                'help'                        => $helpAmountMessage,
                'help_translation_parameters' => $helpAmountMessageParameters,
                'label'                       => 'form.admin.application_payment.amount.label',
                'priority'                    => 300,
            ])
            ->add('type', ApplicationPaymentTypeType::class, [
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'label'    => 'form.admin.application_payment.type',
                'priority' => 200,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'  => ApplicationPaymentData::class,
            'application' => null,
        ]);

        $resolver->setAllowedTypes('application', ['null', Application::class]);
    }
}