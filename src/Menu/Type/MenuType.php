<?php

namespace App\Menu\Type;

/**
 * Main menu type implementation which should fit most use cases.
 */
class MenuType implements MenuTypeInterface
{
    private string $identifier;
    private ?string $text;
    private string $url;
    private int $priority = 0;
    private ?MenuTypeInterface $parent = null;
    private array $children = [];
    private bool $active = false;
    private string $block;

    public function __construct(string $identifier, string $block, ?string $text = null, string $url = '#')
    {
        $this->identifier = $identifier;
        $this->block = $block;
        $this->text = $text;
        $this->url = $url;
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
    public function setActive(bool $active = true, bool $ancestorsToo = true): self
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
    public function setParent(?MenuTypeInterface $parent): self
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
    public function getParent(): ?MenuTypeInterface
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function addChild(MenuTypeInterface $child): self
    {
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
     * @inheritDoc
     */
    public function removeChild(string|MenuTypeInterface $child): self
    {
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
     * @inheritDoc
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
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
}