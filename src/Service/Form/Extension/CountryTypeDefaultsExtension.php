<?php

namespace App\Service\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Country type extension that adds preferred countries.
 */
class CountryTypeDefaultsExtension extends AbstractTypeExtension
{
    private array $preferredCountries;

    public function __construct(array $preferredCountries)
    {
        $this->preferredCountries = $preferredCountries;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'preferred_choices' => $this->preferredCountries,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CountryType::class];
    }
}