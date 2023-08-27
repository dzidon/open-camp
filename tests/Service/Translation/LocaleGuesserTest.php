<?php

namespace App\Tests\Service\Translation;

use App\Service\Translation\LocaleGuesser;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests the class that guesses user's locale using a request object.
 */
class LocaleGuesserTest extends KernelTestCase
{
    /**
     * Tests various forms of the accept-language header.
     *
     * @return void
     * @throws Exception
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
        $this->assertSame('cs', $guessedLocale);

        $request = $this->createRequest('fr;q=0.8,es;q=0.7,de;q=0.6');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('de', $guessedLocale);

        $request = $this->createRequest('fr ;q= 0.8 , es ;q= 0.7 , de ;q= 0.6');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('de', $guessedLocale);

        $request = $this->createRequest('fr;q=0.8,es;q=0.7,*;q=0.6,de;q=0.5');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('cs', $guessedLocale);

        $request = $this->createRequest('*');
        $guessedLocale = $guesser->guessLocale($request);
        $this->assertSame('cs', $guessedLocale);

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
     * @throws Exception
     */
    private function createLocaleGuesser(): LocaleGuesser
    {
        $container = static::getContainer();

        /** @var LocaleGuesser $localeGuesser */
        $localeGuesser = $container->get(LocaleGuesser::class);

        return $localeGuesser;
    }
}