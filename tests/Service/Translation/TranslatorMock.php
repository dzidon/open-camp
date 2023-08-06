<?php

namespace App\Tests\Service\Translation;

use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorMock implements TranslatorInterface
{
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $id;
    }

    public function getLocale(): string
    {
        return '';
    }
}