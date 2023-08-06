<?php

namespace App\Library\DataStructure;

/**
 * Stack data structure (LIFO).
 */
interface StackInterface
{
    /**
     * Returns true if the stack is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Returns the number of elements in the stack.
     *
     * @return int
     */
    public function length(): int;

    /**
     * Inserts an element into the stack.
     *
     * @param mixed $element
     * @return $this
     */
    public function push(mixed $element): self;

    /**
     * Returns the last element in the stack and removes it. Returns null if the stack is empty.
     *
     * @return mixed
     */
    public function pop(): mixed;

    /**
     * Returns the last element in the stack. Returns null if the stack is empty.
     *
     * @return mixed
     */
    public function peek(): mixed;
}