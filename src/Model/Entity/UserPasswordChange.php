<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Repository\UserPasswordChangeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Allows users to reset their forgotten password.
 */
#[ORM\Entity(repositoryClass: UserPasswordChangeRepository::class)]
class UserPasswordChange
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $expireAt;

    #[ORM\Column(length: 32)]
    private string $state;

    #[ORM\Column(length: 32, unique: true)]
    private string $selector;

    /**
     * @var string Hashed verifier.
     */
    #[ORM\Column(length: 255)]
    private string $verifier;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(DateTimeImmutable $expireAt, string $selector, string $verifier)
    {
        $this->id = Uuid::v4();
        $this->expireAt = $expireAt;
        $this->selector = $selector;
        $this->verifier = $verifier;
        $this->state = UserPasswordChangeStateEnum::UNUSED->value;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpireAt(): DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(DateTimeImmutable $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getState(): UserPasswordChangeStateEnum
    {
        return UserPasswordChangeStateEnum::tryFrom($this->state);
    }

    public function setState(UserPasswordChangeStateEnum $state): self
    {
        $this->state = $state->value;

        return $this;
    }

    /**
     * Returns true if the user exists, current date time is smaller than expiration date and state is set to unused.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = new DateTimeImmutable('now');

        return $this->user !== null && $now < $this->expireAt && $this->state === UserPasswordChangeStateEnum::UNUSED->value;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function setSelector(string $selector): self
    {
        $this->selector = $selector;

        return $this;
    }

    public function getVerifier(): string
    {
        return $this->verifier;
    }

    public function setVerifier(string $verifier): self
    {
        $this->verifier = $verifier;

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
