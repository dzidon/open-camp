<?php

namespace App\Form\Type\User;

use App\Form\DataTransfer\Data\User\BillingDataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * User profile billing information edit.
 */
class BillingType extends AbstractType
{
    private array $billingCountries;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, array $billingCountries)
    {
        $this->translator = $translator;

        $this->billingCountries = $billingCountries;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countryChoices = [];

        foreach ($this->billingCountries as $countryCode)
        {
            $translationKey = "country.$countryCode";
            $countryName = $this->translator->trans($translationKey);
            $countryChoices[$translationKey] = $countryName;
        }

        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.profile_billing.name',
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
            ->add('country', ChoiceType::class, [
                'choices'     => $countryChoices,
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.user.profile_billing.country',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BillingDataInterface::class,
        ]);
    }
}