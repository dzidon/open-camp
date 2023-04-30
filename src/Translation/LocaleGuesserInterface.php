<?php

namespace App\Translation;

use Symfony\Component\HttpFoundation\Request;

/**
 * This class guesses user's preferred locale (language).
 */
interface LocaleGuesserInterface
{
    /**
     * Guesses user's locale using the Request object.
     *
     * @param Request $request
     * @return string
     */
    public function guessLocale(Request $request): string;
}