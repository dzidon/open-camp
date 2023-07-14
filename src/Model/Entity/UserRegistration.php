<?php

namespace App\Model\Entity;

use App\Enum\Entity\UserRegistrationStateEnum;
use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\UserRegistrationRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Used for registering new users.
 */
#[ORM\Entity(repositoryClass: UserRegistrationRepository::class)]
class UserRegistration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

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

    public function __construct(string $email, DateTimeImmutable $expireAt, string $selector, string $verifier)
    {
        $this->email = $email;
        $this->expireAt = $expireAt;
        $this->selector = $selector;
        $this->verifier = $verifier;
        $this->state = UserRegistrationStateEnum::UNUSED->value;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getState(): UserRegistrationStateEnum
    {
        return UserRegistrationStateEnum::tryFrom($this->state);
    }

    public function setState(UserRegistrationStateEnum $state): self
    {
        $this->state = $state->value;

        return $this;
    }

    /**
     * Returns true if current date time is smaller than expiration date and state is set to unused.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = new DateTimeImmutable('now');

        return $now < $this->expireAt && $this->state === UserRegistrationStateEnum::UNUSED->value;
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
