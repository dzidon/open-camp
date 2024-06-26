<?php

namespace App\Model\Entity;

use App\Library\Enum\GenderEnum;
use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\CamperRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * User camper information.
 */
#[ORM\Entity(repositoryClass: CamperRepository::class)]
class Camper
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $nameFirst;

    #[ORM\Column(length: 255)]
    private string $nameLast;

    #[ORM\Column(length: 1)]
    private string $gender;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $bornAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationalIdentifier = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $dietaryRestrictions = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $healthRestrictions = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $medication = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $nameFirst, string $nameLast, GenderEnum $gender, DateTimeImmutable $bornAt, User $user)
    {
        $this->id = Uuid::v4();
        $this->nameFirst = $nameFirst;
        $this->nameLast = $nameLast;
        $this->gender = $gender->value;
        $this->bornAt = $bornAt;
        $this->user = $user;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getNameFull(): string
    {
        return $this->nameFirst . ' ' . $this->nameLast;
    }

    public function getNameFirst(): string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(string $nameFirst): self
    {
        $this->nameFirst = $nameFirst;

        return $this;
    }

    public function getNameLast(): string
    {
        return $this->nameLast;
    }

    public function setNameLast(string $nameLast): self
    {
        $this->nameLast = $nameLast;

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

    public function getNationalIdentifier(): ?string
    {
        return $this->nationalIdentifier;
    }

    public function setNationalIdentifier(?string $nationalIdentifier): self
    {
        $this->nationalIdentifier = $nationalIdentifier;

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

    public function getMedication(): ?string
    {
        return $this->medication;
    }

    public function setMedication(?string $medication): self
    {
        $this->medication = $medication;

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