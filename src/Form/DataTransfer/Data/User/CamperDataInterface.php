<?php

namespace App\Form\DataTransfer\Data\User;

use App\Enum\GenderEnum;
use DateTimeImmutable;

/**
 * Camper edit data.
 */
interface CamperDataInterface
{
    public function isNationalIdentifierEnabled(): bool;

    public function getNameFirst(): ?string;

    public function setNameFirst(?string $nameFirst): self;

    public function getNameLast(): ?string;

    public function setNameLast(?string $nameLast): self;

    public function getGender(): ?GenderEnum;

    public function setGender(?GenderEnum $gender): self;

    public function getNationalIdentifier(): ?string;

    public function setNationalIdentifier(?string $nationalIdentifier): self;

    public function isNationalIdentifierAbsent(): bool;

    public function setIsNationalIdentifierAbsent(bool $isNationalIdentifierAbsent): self;

    public function getBornAt(): ?DateTimeImmutable;

    public function setBornAt(?DateTimeImmutable $bornAt): self;

    public function getDietaryRestrictions(): ?string;

    public function setDietaryRestrictions(?string $dietaryRestrictions): self;

    public function getHealthRestrictions(): ?string;

    public function setHealthRestrictions(?string $healthRestrictions): self;

    public function getMedication(): ?string;

    public function setMedication(?string $medication): self;

    public function getSiblings(): iterable;

    public function setSiblings(iterable $siblings): self;
}