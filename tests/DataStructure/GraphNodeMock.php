<?php

namespace App\Tests\DataStructure;

use App\DataStructure\GraphNodeInterface;

/**
 * Basic graph node mock for testing.
 */
class GraphNodeMock implements GraphNodeInterface
{
    protected string $identifier;
    protected ?GraphNodeInterface $parent = null;
    protected array $children = [];

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function setParent(?GraphNodeInterface $parent): self
    {
        if ($this->parent === $parent)
        {
            return $this;
        }

        $oldParent = $this->parent;
        $this->parent = $parent;

        if ($parent === null)
        {
            $oldParent->removeChild($this);
        }
        else
        {
            $this->parent->addChild($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?GraphNodeInterface
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function addChild(GraphNodeInterface $child): self
    {
        $identifier = $child->getIdentifier();

        /** @var GraphNodeInterface $existingChild */
        foreach ($this->children as $key => $existingChild)
        {
            if ($child === $existingChild)
            {
                return $this;
            }

            if ($existingChild->getIdentifier() === $identifier)
            {
                $existingChild->setParent(null);
                unset($this->children[$key]);
                break;
            }
        }

        $this->children[] = $child;
        $child->setParent($this);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeChild(string|GraphNodeInterface $child): self
    {
        if (is_string($child))
        {
            $identifier = $child;
        }
        else
        {
            $identifier = $child->getIdentifier();
        }

        /** @var GraphNodeInterface $existingChild */
        foreach ($this->children as $key => $existingChild)
        {
            if ($existingChild->getIdentifier() === $identifier)
            {
                $existingChild->setParent(null);
                unset($this->children[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @inheritDoc
     */
    public function getChild(string $identifier): ?GraphNodeInterface
    {
        /** @var GraphNodeInterface $existingChild */
        foreach ($this->children as $existingChild)
        {
            if ($existingChild->getIdentifier() === $identifier)
            {
                return $existingChild;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasChild(string $identifier): bool
    {
        $child = $this->getChild($identifier);
        if ($child === null)
        {
            return false;
        }

        return true;
    }
}