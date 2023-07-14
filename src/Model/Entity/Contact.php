<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * User contact information.
 */
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(type: 'phone_number')]
    private PhoneNumber $phoneNumber;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, string $email, PhoneNumber $phoneNumber, User $user)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phoneNumber = clone $phoneNumber;
        $this->user = $user;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return clone $this->phoneNumber;
    }

    public function setPhoneNumber(PhoneNumber $phoneNumber): self
    {
        if ($this->phoneNumber->equals($phoneNumber))
        {
            return $this;
        }

        $this->phoneNumber = clone $phoneNumber;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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