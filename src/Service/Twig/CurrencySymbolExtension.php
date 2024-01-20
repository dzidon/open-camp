<?php

namespace App\Service\Twig;

use Symfony\Component\Intl\Currencies;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds the ability to return symbols of currencies to Twig.
 */
class CurrencySymbolExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('currency_symbol_for_code', [$this, 'getCurrencySymbolForCode']),
        ];
    }

    /**
     * Returns the name that corresponds with the given country code.
     *
     * @param string $currencyCode
     * @param string|null $displayLocale
     * @return string
     */
    public function getCurrencySymbolForCode(string $currencyCode, string $displayLocale = null): string
    {
        if (!Currencies::exists($currencyCode))
        {
            return '';
        }

        return Currencies::getSymbol($currencyCode, $displayLocale);
    }
}