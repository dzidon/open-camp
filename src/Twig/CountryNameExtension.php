<?php

namespace App\Twig;

use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds the ability to translate country codes to Twig.
 */
class CountryNameExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('country_name_for_code', [$this, 'getCountryNameForCode']),
        ];
    }

    /**
     * Returns the name that corresponds with the given country code.
     *
     * @param string $countryCode
     * @return string
     */
    public function getCountryNameForCode(string $countryCode): string
    {
        try
        {
            return Countries::getName($countryCode);
        }
        catch (MissingResourceException)
        {
            return $countryCode;
        }
    }
}