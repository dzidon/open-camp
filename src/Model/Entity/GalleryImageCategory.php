<?php

namespace App\Model\Entity;

use App\Library\DataStructure\SortableTreeNodeInterface;
use App\Library\DataStructure\TreeNodeInterface;
use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\GalleryImageCategoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Gallery image category.
 */
#[ORM\Entity(repositoryClass: GalleryImageCategoryRepository::class)]
class GalleryImageCategory implements SortableTreeNodeInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $urlName;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\ManyToOne(targetEntity: GalleryImageCategory::class, inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?GalleryImageCategory $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: GalleryImageCategory::class)]
    private Collection $children;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, string $urlName, int $priority)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->urlName = $urlName;
        $this->priority = $priority;
        $this->children = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrlName(): string
    {
        return $this->urlName;
    }

    public function setUrlName(string $urlName): self
    {
        $this->urlName = $urlName;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getIdentifier(): UuidV4
    {
        return $this->id;
    }

    public function getParent(): ?GalleryImageCategory
    {
        return $this->parent;
    }

    /**
     * @param GalleryImageCategory|null $parent
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

    public function getChildren(): array
    {
        return $this->children->toArray();
    }

    /**
     * @param GalleryImageCategory $child
     * @return $this
     */
    public function addChild(TreeNodeInterface $child): self
    {
        $this->assertSelfReferencedType($child);

        $identifier = $child
            ->getIdentifier()
            ->toRfc4122()
        ;

        /** @var GalleryImageCategory $existingChild */
        foreach ($this->children as $key => $existingChild)
        {
            if ($child === $existingChild)
            {
                return $this;
            }

            $existingChildIdentifier = $existingChild
                ->getIdentifier()
                ->toRfc4122()
            ;

            if ($existingChildIdentifier === $identifier)
            {
                $existingChild->setParent(null);
                $this->children->remove($key);

                break;
            }
        }

        $this->children->add($child);
        $child->setParent($this);

        return $this;
    }

    /**
     * @param GalleryImageCategory|string $child Instance or a Rfc4122 string
     * @return $this
     */
    public function removeChild(TreeNodeInterface|string $child): self
    {
        $this->assertSelfReferencedType($child);

        if (is_string($child))
        {
            $identifier = $child;
        }
        else
        {
            $identifier = $child
                ->getIdentifier()
                ->toRfc4122()
            ;
        }

        /** @var GalleryImageCategory $existingChild */
        foreach ($this->children as $key => $existingChild)
        {
            $existingChildIdentifier = $existingChild
                ->getIdentifier()
                ->toRfc4122()
            ;

            if ($existingChildIdentifier === $identifier)
            {
                $existingChild->setParent(null);
                $this->children->remove($key);

                break;
            }
        }

        return $this;
    }

    /**
     * @param string $identifier Rfc4122 string
     * @return GalleryImageCategory|null
     */
    public function getChild(string $identifier): ?GalleryImageCategory
    {
        /** @var GalleryImageCategory $existingChild */
        foreach ($this->children as $existingChild)
        {
            $existingChildIdentifier = $existingChild
                ->getIdentifier()
                ->toRfc4122()
            ;

            if ($existingChildIdentifier === $identifier)
            {
                return $existingChild;
            }
        }

        return null;
    }

    /**
     * @param string $identifier Rfc4122 string
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

    public function sortChildren(): self
    {
        $children = $this->children->toArray();

        if (!empty($children))
        {
            foreach ($children as $child)
            {
                $this->children->removeElement($child);
            }

            usort($children, function (GalleryImageCategory $a, GalleryImageCategory $b)
            {
                return $b->getPriority() <=> $a->getPriority();
            });

            foreach ($children as $child)
            {
                $this->children->add($child);
            }
        }

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return GalleryImageCategory[]
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $expandedNodes = [];
        $currentNode = $this->getParent();

        while ($currentNode !== null)
        {
            foreach ($expandedNodes as $expandedNode)
            {
                if ($currentNode->getId()->toRfc4122() === $expandedNode->getId()->toRfc4122())
                {
                    throw new LogicException(sprintf('Found a cycle in "%s".', __METHOD__));
                }
            }

            array_unshift($ancestors, $currentNode);

            $expandedNodes[] = $currentNode;
            $currentNode = $currentNode->getParent();
        }

        return $ancestors;
    }

    public function getPath(bool $useHumanName = false): string
    {
        $path = '';
        $expandedNodes = [];
        $currentNode = $this;

        while ($currentNode !== null)
        {
            foreach ($expandedNodes as $expandedNode)
            {
                if ($currentNode->getId()->toRfc4122() === $expandedNode->getId()->toRfc4122())
                {
                    throw new LogicException(sprintf('Found a cycle in "%s".', __METHOD__));
                }
            }

            if ($useHumanName)
            {
                $pathPart = $currentNode->getName();
            }
            else
            {
                $pathPart = $currentNode->getUrlName();
            }

            if ($path === '')
            {
                $path = $pathPart;
            }
            else
            {
                $path = $pathPart . '/' . $path;
            }

            $expandedNodes[] = $currentNode;
            $currentNode = $currentNode->getParent();
        }

        return $path;
    }

    /**
     * Throws a LogicException if the specified node type is not supported in a child/parent relationship.
     *
     * @param mixed $treeNode
     * @return void
     */
    protected function assertSelfReferencedType(mixed $treeNode): void
    {
        if (is_object($treeNode) && !$treeNode instanceof GalleryImageCategory)
        {
            throw new LogicException(
                sprintf('%s cannot be used as a parent/child with %s.', $treeNode::class, $this::class)
            );
        }
    }
}