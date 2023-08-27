<?php

namespace App\Service\Form\Extension;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Phone number type extension that sets phone number region and phone number format.
 */
class PhoneNumberTypeDefaultsExtension extends AbstractTypeExtension
{
    private string $phoneNumberDefaultLocale;
    private int $phoneNumberFormat;

    public function __construct(string $phoneNumberDefaultLocale, int $phoneNumberFormat)
    {
        $this->phoneNumberDefaultLocale = $phoneNumberDefaultLocale;
        $this->phoneNumberFormat = $phoneNumberFormat;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_region' => $this->phoneNumberDefaultLocale,
            'format'         => $this->phoneNumberFormat,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [PhoneNumberType::class];
    }
}