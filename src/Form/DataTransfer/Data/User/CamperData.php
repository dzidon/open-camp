<?php

namespace App\Form\DataTransfer\Data\User;

use App\Enum\GenderEnum;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class CamperData implements CamperDataInterface
{
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\NotBlank]
    private ?GenderEnum $gender = null;

    #[Assert\LessThan('today', message: 'date_in_past')]
    #[Assert\NotBlank]
    private ?DateTimeImmutable $bornAt = null;

    #[Assert\Length(max: 1000)]
    private ?string $dietaryRestrictions = null;

    #[Assert\Length(max: 1000)]
    private ?string $healthRestrictions = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
}