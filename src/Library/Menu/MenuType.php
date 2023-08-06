<?php

namespace App\Library\Menu;

use App\Library\DataStructure\TreeNodeInterface;
use LogicException;

/**
 * Main menu type implementation which should fit most use cases.
 */
class MenuType implements MenuTypeInterface
{
    protected string $identifier;
    protected ?MenuTypeInterface $parent = null;
    protected array $children = [];
    protected ?string $text;
    protected string $url;
    protected int $priority = 0;
    protected bool $active = false;
    protected string $block;

    public function __construct(string $identifier, string $block, ?string $text = null, string $url = '#')
    {
        $this->identifier = $identifier;
        $this->block = $block;
        $this->text = $text;
        $this->url = $url;
    }

    /**
     * Returns a unique identifier of the menu type.
     *
     * @return mixed
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Sets the parent menu type.
     *
     * @param MenuTypeInterface|null $parent
     * @return $this
     */
    public function setParent(?TreeNodeInterface $parent): self
    {
        $this->assertSelfReferencedType($parent);

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
     * Returns null or the parent menu type.
     *
     * @return MenuTypeInterface|null
     */
    public function getParent(): ?MenuTypeInterface
    {
        return $this->parent;
    }

    /**
     * Adds a child menu type.
     *
     * @param MenuTypeInterface $child
     * @return $this
     */
    public function addChild(TreeNodeInterface $child): self
    {
        $this->assertSelfReferencedType($child);

        $identifier = $child->getIdentifier();

        /** @var MenuTypeInterface $existingChild */
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
     * Removes a child menu type.
     *
     * @param string|MenuTypeInterface $child Identifier or instance.
     * @return $this
     */
    public function removeChild(string|TreeNodeInterface $child): self
    {
        $this->assertSelfReferencedType($child);

        if (is_string($child))
        {
            $identifier = $child;
        }
        else
        {
            $identifier = $child->getIdentifier();
        }

        /** @var MenuTypeInterface $existingChild */
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
     * Returns all children menu types.
     *
     * @return MenuTypeInterface[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Returns a child menu type using its identifier.
     *
     * @param string $identifier
     * @return MenuTypeInterface|null
     */
    public function getChild(string $identifier): ?MenuTypeInterface
    {
        /** @var MenuTypeInterface $existingChild */
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
     * Returns true if the menu type has a child with the specified identifier.
     *
     * @param string $identifier
     * @return bool
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

    /**
     * @inheritDoc
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @inheritDoc
     */
    public function setActive(bool $active = true, bool $ancestorsToo = false): self
    {
        $this->active = $active;

        if ($ancestorsToo && $this->parent !== null)
        {
            $visited = [$this];
            $currentMenuType = $this->parent;
            while ($currentMenuType !== null && !in_array($currentMenuType, $visited, true))
            {
                $visited[] = $currentMenuType;
                $currentMenuType->setActive($active, false);
                $currentMenuType = $currentMenuType->getParent();
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateBlock(): string
    {
        return $this->block;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateBlock(string $block): self
    {
        $this->block = $block;
        return $this;
    }

    /**
     * Sorts its children using the priority attribute in descending order.
     *
     * @return $this
     */
    public function sortChildren(): self
    {
        if (!empty($this->children))
        {
            usort($this->children, function (MenuTypeInterface $a, MenuTypeInterface $b)
            {
                return $b->getPriority() <=> $a->getPriority();
            });
        }

        return $this;
    }

    /**
     * Throws a LogicException if the specified node type is not supported in a child/parent relationship.
     *
     * @param mixed $treeNode
     * @return void
     */
    protected function assertSelfReferencedType(mixed $treeNode): void
    {
        if (is_object($treeNode) && !$treeNode instanceof MenuTypeInterface)
        {
            throw new LogicException(
                sprintf('%s cannot be used as a parent/child with %s.', $treeNode::class, $this::class)
            );
        }
    }
}