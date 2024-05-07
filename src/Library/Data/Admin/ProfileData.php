<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileData
{
    private ?User $user;

    #[Assert\Length(max: 255)]
    #[Assert\When(
        expression: 'this.getNameLast() !== null',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?string $nameFirst = null;

    #[Assert\Length(max: 255)]
    #[Assert\When(
        expression: 'this.getNameFirst() !== null',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?string $nameLast = null;

    #[Assert\LessThan('today', message: 'date_in_past')]
    private ?DateTimeImmutable $bornAt = null;

    #[Assert\Length(max: 64)]
    private ?string $bioShort = null;

    #[Assert\Length(max: 2000)]
    private ?string $bio = null;

    #[Assert\Image]
    private ?File $image = null;

    private bool $removeImage = false;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
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

    public function getBornAt(): ?DateTimeImmutable
    {
        return $this->bornAt;
    }

    public function setBornAt(?DateTimeImmutable $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    public function getBioShort(): ?string
    {
        return $this->bioShort;
    }

    public function setBioShort(?string $bioShort): self
    {
        $this->bioShort = $bioShort;

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