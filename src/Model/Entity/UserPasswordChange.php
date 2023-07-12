<?php

namespace App\Model\Entity;

use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Repository\UserPasswordChangeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Allows users to reset their forgotten password.
 */
#[ORM\Entity(repositoryClass: UserPasswordChangeRepository::class)]
class UserPasswordChange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    public function __construct(DateTimeImmutable $expireAt, string $selector, string $verifier)
    {
        $this->expireAt = $expireAt;
        $this->selector = $selector;
        $this->verifier = $verifier;
        $this->state = UserPasswordChangeStateEnum::UNUSED->value;
    }

    public function getId(): ?int
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
}
