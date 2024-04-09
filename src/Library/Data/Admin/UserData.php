<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\UniqueUserEmail;
use App\Library\Constraint\UniqueUserUrlName;
use App\Library\Data\Common\BillingData;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueUserUrlName]
#[UniqueUserEmail]
class UserData
{
    private ?User $user;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    private ?Role $role = null;

    #[Assert\Valid]
    private BillingData $billingData;

    private bool $isFeaturedGuide = false;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    private ?string $urlName = null;

    #[Assert\NotBlank]
    private ?int $guidePriority = 0;

    #[Assert\LessThan('today', message: 'date_in_past')]
    private ?DateTimeImmutable $bornAt = null;

    #[Assert\Length(max: 2000)]
    private ?string $bio = null;

    #[Assert\Image]
    private ?File $image = null;

    private bool $removeImage = false;

    public function __construct(bool $isEuBusinessDataEnabled, ?User $user = null)
    {
        $this->billingData = new BillingData(false, $isEuBusinessDataEnabled);
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getBillingData(): BillingData
    {
        return $this->billingData;
    }

    public function isFeaturedGuide(): bool
    {
        return $this->isFeaturedGuide;
    }

    public function setIsFeaturedGuide(bool $isFeaturedGuide): self
    {
        $this->isFeaturedGuide = $isFeaturedGuide;

        return $this;
    }

    public function getUrlName(): ?string
    {
        return $this->urlName;
    }

    public function setUrlName(?string $urlName): self
    {
        $this->urlName = $urlName;

        return $this;
    }

    public function getGuidePriority(): ?int
    {
        return $this->guidePriority;
    }

    public function setGuidePriority(?int $guidePriority): self
    {
        $this->guidePriority = $guidePriority;

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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function removeImage(): bool
    {
        return $this->removeImage;
    }

    public function setRemoveImage(bool $removeImage): self
    {
        $this->removeImage = $removeImage;

        return $this;
    }
}