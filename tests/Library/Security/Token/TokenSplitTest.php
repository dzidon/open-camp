<?php

namespace App\Tests\Library\Security\Token;

use App\Library\Security\Token\TokenSplit;
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