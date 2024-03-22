<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\BillingData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Billing information edit.
 */
class BillingType extends AbstractType
{
    private bool $isEuBusinessDataEnabled;

    public function __construct(bool $isEuBusinessDataEnabled)
    {
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameFirst', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.profile_billing.name_first',
            ])
            ->add('nameLast', TextType::class, [
                'required' => false,
                'label'    => 'form.user.profile_billing.name_last',
            ])
            ->add('street', TextType::class, [
                'required' => false,
                'label'    => 'form.user.profile_billing.street',
            ])
            ->add('town', TextType::class, [
                'required' => false,
                'label'    => 'form.user.profile_billing.town',
            ])
            ->add('zip', TextType::class, [
                'required' => false,
                'label'    => 'form.user.profile_billing.zip',
            ])
            ->add('country', CountryType::class, [
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.user.profile_billing.country',
            ])
        ;

        if ($this->isEuBusinessDataEnabled)
        {
            $builder
                ->add('isCompany', CheckboxType::class, [
                    'label'    => 'form.user.profile_billing.is_company',
                    'attr'     => [
                        'data-controller'                      => 'cv--checkbox',
                        'data-action'                          => 'cv--checkbox#updateVisibility',
                        'data-cv--checkbox-cv--content-outlet' => '.company-fields-visibility',
                    ],
                    'required' => false,
                ])
                ->add('businessName', TextType::class, [
                    'label'      => 'form.user.profile_billing.business_name',
                    'row_attr' => [
                        'class'                                   => 'company-fields-visibility',
                        'data-controller'                         => 'cv--content',
                        'data-cv--content-show-when-chosen-value' => '1',
                    ],
                    'required' => false,
                ])
                ->add('businessCin', TextType::class, [
                    'label'      => 'form.user.profile_billing.business_cin',
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
                    'label'      => 'form.user.profile_billing.business_vat_id',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => BillingData::class,
            'block_prefix' => 'common_billing',
        ]);
    }
}