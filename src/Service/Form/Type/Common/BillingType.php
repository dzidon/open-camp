<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\BillingData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Billing information edit.
 */
class BillingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isContactAutofillEnabled = $options['enable_contact_autofill'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($isContactAutofillEnabled): void
            {
                /** @var null|BillingData $billingData */
                $billingData = $event->getData();
                $form = $event->getForm();

                if ($billingData === null)
                {
                    return;
                }

                $isRequired = $billingData->isRequired();
                $isEuBusinessDataEnabled = $billingData->isEuBusinessDataEnabled();

                $nameFirstAttr = ['autofocus' => 'autofocus'];
                $nameLastAttr = [];

                if ($isContactAutofillEnabled)
                {
                    $nameFirstAttr['data-app--contact-autofill-target'] = 'nameFirst';
                    $nameFirstAttr['data-action'] = 'app--contact-autofill#fillFirstContact';

                    $nameLastAttr['data-app--contact-autofill-target'] = 'nameLast';
                    $nameLastAttr['data-action'] = 'app--contact-autofill#fillFirstContact';
                }

                $form
                    ->add('nameFirst', TextType::class, [
                        'attr'     => $nameFirstAttr,
                        'required' => $isRequired,
                        'label'    => 'form.common.billing.name_first',
                    ])
                    ->add('nameLast', TextType::class, [
                        'attr'     => $nameLastAttr,
                        'required' => $isRequired,
                        'label'    => 'form.common.billing.name_last',
                    ])
                    ->add('street', TextType::class, [
                        'required' => $isRequired,
                        'label'    => 'form.common.billing.street',
                    ])
                    ->add('town', TextType::class, [
                        'required' => $isRequired,
                        'label'    => 'form.common.billing.town',
                    ])
                    ->add('zip', TextType::class, [
                        'required' => $isRequired,
                        'label'    => 'form.common.billing.zip',
                    ])
                    ->add('country', CountryType::class, [
                        'placeholder' => 'form.common.choice.none.female',
                        'required'    => $isRequired,
                        'label'       => 'form.common.billing.country',
                    ])
                ;

                if ($isEuBusinessDataEnabled)
                {
                    $form
                        ->add('isCompany', CheckboxType::class, [
                            'label'    => 'form.common.billing.is_company',
                            'attr'     => [
                                'data-controller'                      => 'cv--checkbox',
                                'data-action'                          => 'cv--checkbox#updateVisibility',
                                'data-cv--checkbox-cv--content-outlet' => '.company-fields-visibility',
                            ],
                            'required' => false,
                        ])
                        ->add('businessName', TextType::class, [
                            'label'      => 'form.common.billing.business_name',
                            'row_attr' => [
                                'class'                                   => 'company-fields-visibility',
                                'data-controller'                         => 'cv--content',
                                'data-cv--content-show-when-chosen-value' => '1',
                            ],
                            'required' => false,
                        ])
                        ->add('businessCin', TextType::class, [
                            'label'      => 'form.common.billing.business_cin',
                            'label_attr' => [
                                'class' => 'required'
                            ],
                            'row_attr' => [
                                'class'                                   => 'company-fields-visibility',
                                'data-controller'                         => 'cv--content',
                                'data-cv--content-show-when-chosen-value' => '1',
                            ],
                            'required' => false,
                        ])
                        ->add('businessVatId', TextType::class, [
                            'label'      => 'form.common.billing.business_vat_id',
                            'label_attr' => [
                                'class' => 'required'
                            ],
                            'row_attr' => [
                                'class'                                   => 'company-fields-visibility',
                                'data-controller'                         => 'cv--content',
                                'data-cv--content-show-when-chosen-value' => '1',
                            ],
                            'required' => false,
                        ])
                    ;
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => BillingData::class,
            'block_prefix'            => 'common_billing',
            'enable_contact_autofill' => false,
        ]);

        $resolver->setAllowedTypes('enable_contact_autofill', 'bool');
    }
}