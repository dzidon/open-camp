<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Model\Entity\CampCategory;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin camp edit data.
 */
interface CampDataInterface
{
    public function getId(): ?UuidV4;

    public function setId(?UuidV4 $id): self;

    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getUrlName(): ?string;

    public function setUrlName(?string $urlName): self;

    public function getAgeMin(): ?int;

    public function setAgeMin(?int $ageMin): self;

    public function getAgeMax(): ?int;

    public function setAgeMax(?int $ageMax): self;

    public function getDescriptionShort(): ?string;

    public function setDescriptionShort(?string $descriptionShort): self;

    public function getDescriptionLong(): ?string;

    public function setDescriptionLong(?string $descriptionLong): self;

    public function getCampCategory(): ?CampCategory;

    public function setCampCategory(?CampCategory $campCategory): self;
}