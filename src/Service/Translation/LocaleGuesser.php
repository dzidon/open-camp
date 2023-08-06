<?php

namespace App\Service\Translation;

use Symfony\Component\HttpFoundation\Request;

/**
 * @inheritDoc
 */
class LocaleGuesser implements LocaleGuesserInterface
{
    private array $locales;
    private string $defaultLocale;

    public function __construct(array|string $locales, string $defaultLocale)
    {
        if (is_string($locales))
        {
            $locales = explode('|', $locales);
        }

        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Guesses user's locale using the "accept-language" request header. The header is parsed into an array which
     * is then sorted by preference in descending order. The first supported locale from the array is returned.
     * If none of the locales are supported, the default locale is returned.
     *
     * @param Request $request
     * @return string
     */
    public function guessLocale(Request $request): string
    {
        $acceptLanguage = $request->headers->get('accept-language', '*');
        $locales = explode(',', $acceptLanguage);
        $prioritizedLocales = [];

        foreach ($locales as $locale)
        {
            $exploded = explode(';q=', $locale);
            if (!array_key_exists(0, $exploded))
            {
                continue;
            }

            $priority = 1.0;
            if (array_key_exists(1, $exploded))
            {
                $priority = (float) trim($exploded[1]);
            }

            $code = trim($exploded[0]);
            $prioritizedLocales[$code] = $priority;
        }

        arsort($prioritizedLocales);

        $guessedLocale = $this->defaultLocale;
        foreach ($prioritizedLocales as $locale => $priority)
        {
            if (in_array($locale, $this->locales))
            {
                $guessedLocale = $locale;
                break;
            }

            if ($locale === '*')
            {
                break;
            }
        }

        return $guessedLocale;
    }
}