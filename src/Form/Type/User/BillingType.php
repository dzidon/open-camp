<?php

namespace App\Form\Type\User;

use App\Form\DataTransfer\Data\User\BillingDataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User profile billing information edit.
 */
class BillingType extends AbstractType
{
    private array $preferredCountries;

    public function __construct(array $preferredCountries)
    {
        $this->preferredCountries = $preferredCountries;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ->add('country', CountryType::class, [
                'preferred_choices' => $this->preferredCountries,
                'placeholder'       => 'form.common.choice.none.female',
                'required'          => false,
                'label'             => 'form.user.profile_billing.country',
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