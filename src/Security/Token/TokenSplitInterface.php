<?php

namespace App\Security\Token;

/**
 * Represents a token split into two parts: selector & plain text verifier.
 */
interface TokenSplitInterface
{
    /**
     * Returns the selector.
     *
     * @return string
     */
    public function getSelector(): string;

    /**
     * Returns the plain text verifier.
     *
     * @return string
     */
    public function getPlainVerifier(): string;
}