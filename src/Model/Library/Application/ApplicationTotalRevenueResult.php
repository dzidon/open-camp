<?php

namespace App\Model\Library\Application;

use LogicException;
use NumberFormatter;

/**
 * @inheritDoc
 */
class ApplicationTotalRevenueResult implements ApplicationTotalRevenueResultInterface
{
    private string $currentCurrency;

    private array $totals = [];

    private NumberFormatter $numberFormatter;

    public function __construct(string $currentCurrency, string $locale, array $totalsByCurrency)
    {
        $this->currentCurrency = $currentCurrency;

        foreach ($totalsByCurrency as $currency => $total)
        {
            if (!is_string($currency) || !is_float($total))
            {
                throw new LogicException(
                    sprintf('Array passed to %s must be in the following format: ["CZK" => 100.0, "EUR" => 200.0, ...].', __METHOD__)
                );
            }

            $this->totals[$currency] = $total;
        }

        $numberFormatter = numfmt_create($locale, NumberFormatter::CURRENCY);

        if (!$numberFormatter instanceof NumberFormatter)
        {
            throw new LogicException(
                sprintf('Failed to instantiate %s in %s.', NumberFormatter::class, __METHOD__)
            );
        }

        $this->numberFormatter = $numberFormatter;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        if (empty($this->totals))
        {
            return numfmt_format_currency($this->numberFormatter, 0.0, $this->currentCurrency);
        }

        $formattedStrings = [];

        foreach ($this->totals as $currency => $total)
        {
            $formattedString = numfmt_format_currency($this->numberFormatter, $total, $currency);

            if (!empty($formattedString))
            {
                $formattedStrings[] = $formattedString;
            }
        }

        return implode(' + ', $formattedStrings);
    }
}