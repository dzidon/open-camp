<?php

namespace App\Security;

/**
 * @inheritDoc
 */
class TokenSplitter implements TokenSplitterInterface
{
    const HALF_TOKEN_LENGTH = 32;

    /**
     * @inheritDoc
     */
    public function splitToken(string $token): TokenSplit
    {
        if (mb_strlen($token, 'utf-8') <= static::HALF_TOKEN_LENGTH)
        {
            return new TokenSplit($token, '');
        }

        $stringParts = str_split($token, static::HALF_TOKEN_LENGTH);

        return new TokenSplit($stringParts[0], $stringParts[1]);
    }

    /**
     * @inheritDoc
     */
    public function generateTokenSplit(): TokenSplit
    {
        // produces "HALF_TOKEN_LENGTH * 2" characters
        $fullToken = bin2hex(random_bytes(static::HALF_TOKEN_LENGTH));

        return $this->splitToken($fullToken);
    }
}