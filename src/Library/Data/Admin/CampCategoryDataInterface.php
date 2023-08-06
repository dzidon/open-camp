<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\CampCategory;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin camp category edit data.
 */
interface CampCategoryDataInterface
{
    public function getId(): ?UuidV4;

    public function setId(?UuidV4 $id): self;

    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getUrlName(): ?string;

    public function setUrlName(?string $urlName): self;

    public function getParent(): ?CampCategory;

    public function setParent(?CampCategory $parent): self;
}