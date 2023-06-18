<?php

namespace App\Tests\Security\Token;

use App\Security\Token\TokenSplit;
use App\Security\Token\TokenSplitter;

/**
 * Modifies the original TokenSplitter, so it returns pre-defined tokens and can be used for testing.
 */
class TokenSplitterMock extends TokenSplitter
{
    const HALF_TOKEN_LENGTH = 3;

    const TEST_SPLITS_EMPTY = -1;
    const TEST_SPLITS_ALL_USED = -2;

    private array $tokens = [];
    private int $currentToken = self::TEST_SPLITS_EMPTY;

    /**
     * If there is an unused test token, this method will return its split and mark the token as used.
     *
     * If there have been no test tokens added or all of the test tokens have already been returned, this method
     * generates a random TokenSplit just like the parent class.
     *
     * @return TokenSplit
     */
    public function generateTokenSplit(): TokenSplit
    {
        if ($this->currentToken === static::TEST_SPLITS_EMPTY || $this->currentToken === static::TEST_SPLITS_ALL_USED)
        {
            return parent::generateTokenSplit();
        }

        $token = $this->tokens[$this->currentToken];
        $tokenSplit = $this->splitToken($token);

        $this->currentToken++;
        if ($this->currentToken > array_key_last($this->tokens))
        {
            $this->currentToken = static::TEST_SPLITS_ALL_USED;
        }

        return $tokenSplit;
    }

    /**
     * Adds a test token that should be returned by method "generateTokenSplit".
     *
     * @param string $token
     * @return $this
     */
    public function addTestToken(string $token): self
    {
        $this->tokens[] = $token;

        if ($this->currentToken == static::TEST_SPLITS_EMPTY)
        {
            $this->currentToken = 0;
        }
        else if ($this->currentToken == static::TEST_SPLITS_ALL_USED)
        {
            $this->currentToken = array_key_last($this->tokens);
        }

        return $this;
    }
}