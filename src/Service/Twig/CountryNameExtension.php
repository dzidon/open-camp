<?php

namespace App\Service\Twig;

use Symfony\Component\Intl\Countries;
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
     * @param string|null $displayLocale
     * @return string
     */
    public function getCountryNameForCode(string $countryCode, string $displayLocale = null): string
    {
        return Countries::getName($countryCode, $displayLocale);
    }
}