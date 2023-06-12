<?php

namespace App\Tests\Security;

use App\Security\TokenSplitter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the token splitter.
 */
class TokenSplitterTest extends KernelTestCase
{
    public function testSplitToken(): void
    {
        $tokenSplitter = $this->getTokenSplitter();
        $tokenSplit = $tokenSplitter->splitToken('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy');

        $this->assertSame('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', $tokenSplit->getSelector());
        $this->assertSame('yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy', $tokenSplit->getPlainVerifier());
    }

    public function testGenerateTokenSplit(): void
    {
        $tokenSplitter = $this->getTokenSplitter();
        $tokenSplit = $tokenSplitter->generateTokenSplit();

        $this->assertSame(32, mb_strlen($tokenSplit->getSelector(), 'utf-8'));
        $this->assertSame(32, mb_strlen($tokenSplit->getPlainVerifier(), 'utf-8'));
    }

    private function getTokenSplitter(): TokenSplitter
    {
        $container = static::getContainer();

        /** @var TokenSplitter $tokenSplitter */
        $tokenSplitter = $container->get(TokenSplitter::class);

        return $tokenSplitter;
    }
}