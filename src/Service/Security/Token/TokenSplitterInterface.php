<?php

namespace App\Service\Security\Token;

use App\Library\Security\Token\TokenSplitInterface;

/**
 * Splits and generates tokens.
 */
interface TokenSplitterInterface
{
    /**
     * Splits a token into two parts: selector & plain text verifier.
     *
     * @param string $token
     * @return TokenSplitInterface
     */
    public function splitToken(string $token): TokenSplitInterface;

    /**
     * Generates a new random pair of selector & plain text verifier.
     *
     * @return TokenSplitInterface
     */
    public function generateTokenSplit(): TokenSplitInterface;
}