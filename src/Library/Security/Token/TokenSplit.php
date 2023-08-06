<?php

namespace App\Library\Security\Token;

/**
 * @inheritDoc
 */
class TokenSplit implements TokenSplitInterface
{
    private string $selector;
    private string $plainVerifier;

    public function __construct(string $selector, string $plainVerifier)
    {
        $this->selector = $selector;
        $this->plainVerifier = $plainVerifier;
    }

    /**
     * @inheritDoc
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @inheritDoc
     */
    public function getPlainVerifier(): string
    {
        return $this->plainVerifier;
    }
}