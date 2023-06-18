<?php

namespace App\Tests\Security\Token;

use App\Security\Token\TokenSplit;
use PHPUnit\Framework\TestCase;

/**
 * Tests the token split.
 */
class TokenSplitTest extends TestCase
{
    public function testTokenSplit(): void
    {
        $tokenSplit = new TokenSplit('abc', 'xyz');
        $this->assertSame('abc', $tokenSplit->getSelector());
        $this->assertSame('xyz', $tokenSplit->getPlainVerifier());
    }
}