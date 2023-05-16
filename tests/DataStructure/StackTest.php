<?php

namespace App\Tests\DataStructure;

use App\DataStructure\Stack;
use PHPUnit\Framework\TestCase;

/**
 * Test a basic stack data structure.
 */
class StackTest extends TestCase
{
    /**
     * Tests the LIFO policy of a stack.
     *
     * @return void
     */
    public function testStack(): void
    {
        $stack = new Stack();
        $this->assertSame(true, $stack->isEmpty());
        $this->assertSame(0, $stack->length());
        $this->assertSame(null, $stack->peek());
        $this->assertSame(null, $stack->pop());

        $stack->push('abc');
        $this->assertSame(false, $stack->isEmpty());
        $this->assertSame(1, $stack->length());
        $this->assertSame('abc', $stack->peek());

        $stack->push('xyz');
        $this->assertSame(false, $stack->isEmpty());
        $this->assertSame(2, $stack->length());
        $this->assertSame('xyz', $stack->peek());

        $this->assertSame('xyz', $stack->pop());
        $this->assertSame(false, $stack->isEmpty());
        $this->assertSame(1, $stack->length());
        $this->assertSame('abc', $stack->peek());

        $this->assertSame('abc', $stack->pop());
        $this->assertSame(true, $stack->isEmpty());
        $this->assertSame(0, $stack->length());
        $this->assertSame(null, $stack->peek());
    }
}