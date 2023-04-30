<?php

namespace App\Tests\Functional\Translation;

use App\Translation\LocaleGuesser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests the class that guesses user's locale using a request object.
 */
class LocaleGuesserTest extends TestCase
{
    /**
     * Tests various forms of the accept-language header.
     *
     * @return void
     */
    public function testGuessLocale(): void
    {
        $guesser = $this->createLocaleGuesser();

        $request = $this->createRequest('cs-CZ,cs;q=0.9,en;q=0.8,sk;q=0.7');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('cs', $guessedLocale);

        $request = $this->createRequest('sk;q=0.7,en;q=0.8,cs;q=0.9,cs-CZ');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('cs', $guessedLocale);

        $request = $this->createRequest('fr;q=0.8,es;q=0.7');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('en', $guessedLocale);

        $request = $this->createRequest('fr;q=0.8,es;q=0.7,de;q=0.6');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('de', $guessedLocale);

        $request = $this->createRequest('fr ;q= 0.8 , es ;q= 0.7 , de ;q= 0.6');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('de', $guessedLocale);

        $request = $this->createRequest('fr;q=0.8,es;q=0.7,*;q=0.6,de;q=0.5');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('en', $guessedLocale);

        $request = $this->createRequest('*');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('en', $guessedLocale);

        $request = $this->createRequest('cs');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('cs', $guessedLocale);
    }

    /**
     * Creates a request object with the specified accept-language header.
     *
     * @param string|null $acceptLanguage
     * @return Request
     */
    private function createRequest(string|null $acceptLanguage): Request
    {
        $request = new Request();
        if ($acceptLanguage !== null)
        {
            $request->headers->set('accept-language', $acceptLanguage);
        }

        return $request;
    }

    /**
     * Instantiates the locale guesser.
     *
     * @return LocaleGuesser
     */
    private function createLocaleGuesser(): LocaleGuesser
    {
        $locales = ['en', 'cs', 'de'];
        $defaultLocale = 'en';

        return new LocaleGuesser($locales, $defaultLocale);
    }
}