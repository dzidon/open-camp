<?php

namespace App\Form\DataTransfer\Data\User;

use App\Enum\GenderEnum;
use DateTimeImmutable;

/**
 * Camper edit data.
 */
interface CamperDataInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getGender(): ?GenderEnum;

    public function setGender(?GenderEnum $gender): self;

    public function getBornAt(): ?DateTimeImmutable;

    public function setBornAt(?DateTimeImmutable $bornAt): self;

    public function getDietaryRestrictions(): ?string;

    public function setDietaryRestrictions(?string $dietaryRestrictions): self;

    public function getHealthRestrictions(): ?string;

    public function setHealthRestrictions(?string $healthRestrictions): self;
}