<?php

namespace App\DataStructure;

/**
 * @inheritDoc
 */
class Stack implements StackInterface
{
    private int $topIndex = -1;
    private array $elements = [];

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return $this->topIndex === -1;
    }

    /**
     * @inheritDoc
     */
    public function length(): int
    {
        return $this->topIndex + 1;
    }

    /**
     * @inheritDoc
     */
    public function push(mixed $element): self
    {
        $this->topIndex++;
        $this->elements[$this->topIndex] = $element;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function pop(): mixed
    {
        if ($this->isEmpty())
        {
            return null;
        }

        $topElement = $this->elements[$this->topIndex];
        $this->topIndex--;

        return $topElement;
    }

    /**
     * @inheritDoc
     */
    public function peek(): mixed
    {
        if ($this->isEmpty())
        {
            return null;
        }

        return $this->elements[$this->topIndex];
    }
}