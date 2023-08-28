<?php

namespace App\Library\Data\User;

use App\Library\Constraint\NationalIdentifier;
use App\Library\Enum\GenderEnum;
use App\Model\Entity\Camper;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CamperData
{
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameFirst = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameLast = null;

    #[Assert\NotBlank]
    private ?GenderEnum $gender = null;

    #[Assert\When(
        expression: 'this.isNationalIdentifierEnabled() and not this.isNationalIdentifierAbsent()',
        constraints: [
            new Assert\Length(max: 255),
            new NationalIdentifier(),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $nationalIdentifier = null;

    private bool $isNationalIdentifierAbsent = false;

    #[Assert\LessThan('today', message: 'date_in_past')]
    #[Assert\NotBlank]
    private ?DateTimeImmutable $bornAt = null;

    #[Assert\Length(max: 1000)]
    private ?string $dietaryRestrictions = null;

    #[Assert\Length(max: 1000)]
    private ?string $healthRestrictions = null;

    #[Assert\Length(max: 1000)]
    private ?string $medication = null;

    /**
     * @var Camper[]
     */
    private array $siblings = [];

    private bool $isNationalIdentifierEnabled;

    public function __construct(bool $isNationalIdentifierEnabled)
    {
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
    }

    public function isNationalIdentifierEnabled(): bool
    {
        return $this->isNationalIdentifierEnabled;
    }

    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(?string $nameFirst): self
    {
        $this->nameFirst = $nameFirst;

        return $this;
    }

    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    public function setNameLast(?string $nameLast): self
    {
        $this->nameLast = $nameLast;

        return $this;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function setGender(?GenderEnum $gender): self
    {
        $this->gender = $gender;

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

    public function isNationalIdentifierAbsent(): bool
    {
        return $this->isNationalIdentifierAbsent;
    }

    public function setIsNationalIdentifierAbsent(bool $isNationalIdentifierAbsent): self
    {
        $this->isNationalIdentifierAbsent = $isNationalIdentifierAbsent;

        return $this;
    }

    public function getBornAt(): ?DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function setBornAt(?DateTimeImmutable $bornAt): self
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

    public function getMedication(): ?string
    {
        return $this->medication;
    }

    public function setMedication(?string $medication): self
    {
        $this->medication = $medication;

        return $this;
    }

    public function getSiblings(): array
    {
        return $this->siblings;
    }

    public function addSibling(Camper $sibling): self
    {
        if (in_array($sibling, $this->siblings))
        {
            return $this;
        }

        $this->siblings[] = $sibling;

        return $this;
    }

    public function removeSibling(Camper $sibling): self
    {
        $key = array_search($sibling, $this->siblings, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->siblings[$key]);

        return $this;
    }
}