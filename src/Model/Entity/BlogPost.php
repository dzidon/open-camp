<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\BlogPostRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Blog post entity.
 */
#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 32)]
    private string $title;

    #[ORM\Column(length: 255, unique: true)]
    private string $urlName;

    #[ORM\Column(length: 160)]
    private string $description;

    #[ORM\Column(length: 5000)]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isHidden = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isPinned = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $author;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $title, string $urlName, string $description, string $content, User $author)
    {
        $this->id = Uuid::v4();
        $this->title = $title;
        $this->urlName = $urlName;
        $this->description = $description;
        $this->content = $content;
        $this->author = $author;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
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