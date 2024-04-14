<?php

namespace App\Model\Entity;

use App\Model\Repository\BlogPostViewRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Blog post view entity.
 */
#[ORM\Entity(repositoryClass: BlogPostViewRepository::class)]
class BlogPostView
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: BlogPost::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private BlogPost $blogPost;

    #[ORM\Column(type: UuidType::NAME, nullable: true)]
    private ?UuidV4 $visitorId;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct(BlogPost $blogPost, ?UuidV4 $visitorId = null)
    {
        $this->id = Uuid::v4();
        $this->blogPost = $blogPost;
        $this->visitorId = $visitorId;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function getVisitorId(): ?UuidV4
    {
        return $this->visitorId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}