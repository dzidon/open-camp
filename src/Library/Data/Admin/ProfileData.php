<?php

namespace App\Library\Data\Admin;

use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

class ProfileData
{
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

    #[AssertPhoneNumber]
    private ?PhoneNumber $leaderPhoneNumber = null;

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

    public function getLeaderPhoneNumber(): ?PhoneNumber
    {
        if ($this->leaderPhoneNumber !== null)
        {
            return clone $this->leaderPhoneNumber;
        }

        return $this->leaderPhoneNumber;
    }

    public function setLeaderPhoneNumber(?PhoneNumber $leaderPhoneNumber): self
    {
        if ($leaderPhoneNumber !== null)
        {
            $leaderPhoneNumber = clone $leaderPhoneNumber;
        }

        $this->leaderPhoneNumber = $leaderPhoneNumber;

        return $this;
    }
}