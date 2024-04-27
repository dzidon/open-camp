<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\PageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Custom web page created by an administrator.
 */
#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 32)]
    private string $title;

    #[ORM\Column(length: 255, unique: true)]
    private string $urlName;

    #[ORM\Column(length: 5000)]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isHidden = false;

    /**
     * @var int[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $menuPriorities = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $title, string $urlName, string $content)
    {
        $this->id = Uuid::v4();
        $this->title = $title;
        $this->urlName = $urlName;
        $this->content = $content;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function getMenuPriorities(): array
    {
        return $this->menuPriorities;
    }

    public function getMenuIdentifiers(): array
    {
        return array_keys($this->menuPriorities);
    }

    public function getMenuPriority(string $menuIdentifier): ?int
    {
        if (!array_key_exists($menuIdentifier, $this->menuPriorities))
        {
            return null;
        }

        return $this->menuPriorities[$menuIdentifier];
    }

    public function setMenuPriorities(array $menuPriorities): self
    {
        $exception = new LogicException(
            sprintf('Value passed to %s must be an array with strings as keys and integers as values.', __METHOD__)
        );

        foreach ($menuPriorities as $menuIdentifier => $priority)
        {
            if (!is_string($menuIdentifier) || !is_int($priority))
            {
                throw $exception;
            }
        }

        $this->menuPriorities = $menuPriorities;

        return $this;
    }

    public function setMenuPriority(string $menuIdentifier, int $priority): self
    {
        $this->menuPriorities[$menuIdentifier] = $priority;

        return $this;
    }

    public function removeMenuPriority(string $menuIdentifier): self
    {
        if (array_key_exists($menuIdentifier, $this->menuPriorities))
        {
            unset($this->menuPriorities[$menuIdentifier]);
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
}