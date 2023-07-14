<?php

namespace App\Model\Entity;

use App\Enum\GenderEnum;
use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\CamperRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * User camper information.
 */
#[ORM\Entity(repositoryClass: CamperRepository::class)]
class Camper
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 1)]
    private string $gender;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $bornAt;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $dietaryRestrictions = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $healthRestrictions = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, GenderEnum $gender, DateTimeImmutable $bornAt, User $user)
    {
        $this->name = $name;
        $this->gender = $gender->value;
        $this->bornAt = $bornAt;
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

    public function getGender(): GenderEnum
    {
        return GenderEnum::tryFrom($this->gender);
    }

    public function setGender(GenderEnum $gender): self
    {
        $this->gender = $gender->value;

        return $this;
    }

    public function getBornAt(): DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function setBornAt(DateTimeImmutable $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getDietaryRestrictions(): ?string
    {
        return $this->dietaryRestrictions;
    }

    public function setDietaryRestrictions(?string $dietaryRestrictions): self
    {
        $this->dietaryRestrictions = $dietaryRestrictions;

        return $this;
    }

    public function getHealthRestrictions(): ?string
    {
        return $this->healthRestrictions;
    }

    public function setHealthRestrictions(?string $healthRestrictions): self
    {
        $this->healthRestrictions = $healthRestrictions;

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