<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationDiscountsData;
use App\Model\Library\DiscountConfig\DiscountConfigArrayValidator;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Traversable;

/**
 * Application discounts edit.
 */
class ApplicationDiscountsType extends AbstractType implements DataMapperInterface
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
        $builder->setDataMapper($this);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var ApplicationDiscountsData $data */
            $data = $event->getData();
            $form = $event->getForm();

            $siblingsConfig = $data->getDiscountSiblingsConfig();

            if (empty($siblingsConfig))
            {
                return;
            }

            $nullOptionLabel = $this->translator->trans('form.common.application_discounts.siblings_null_option');

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

                if (!DiscountConfigArrayValidator::isSiblingDiscountIntervalEligibleForNumberOfCampers($from, $to, $numberOfApplicationCampers))
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
                    'label'                     => 'form.common.application_discounts.label',
                    'choice_translation_domain' => false,
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationDiscountsData::class,
        ]);
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms)
    {
        if ($viewData === null)
        {
            return;
        }

        if (!$viewData instanceof ApplicationDiscountsData)
        {
            throw new UnexpectedTypeException($viewData, ApplicationDiscountsData::class);
        }

        /** @var ApplicationDiscountsData $applicationDiscountsData */
        /** @var FormInterface[] $forms */
        $applicationDiscountsData = $viewData;
        $forms = iterator_to_array($forms);

        if (array_key_exists('discountSiblingsInterval', $forms))
        {
            $discountSiblingsInterval = json_encode($applicationDiscountsData->getDiscountSiblingsInterval());
            $forms['discountSiblingsInterval']->setData($discountSiblingsInterval);
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData)
    {
        /** @var ApplicationDiscountsData $applicationDiscountsData */
        /** @var FormInterface[] $forms */
        $applicationDiscountsData = $viewData;
        $forms = iterator_to_array($forms);

        if (array_key_exists('discountSiblingsInterval', $forms))
        {
            $formDiscountSiblingsInterval = $forms['discountSiblingsInterval']->getData();
            $discountSiblingsInterval = json_decode($formDiscountSiblingsInterval, true);
            $applicationDiscountsData->setDiscountSiblingsInterval($discountSiblingsInterval);
        }
    }
}