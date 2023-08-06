<?php

namespace App\Library\Data\User;

use App\Model\Enum\Entity\ContactRoleEnum;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class ContactData implements ContactDataInterface
{
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameFirst = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameLast = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\When(
        expression: 'this.getPhoneNumber() === null',
        constraints: [
            new Assert\NotBlank(message: 'email_or_phone_number')
        ],
    )]
    private ?string $email = null;

    #[AssertPhoneNumber]
    #[Assert\When(
        expression: 'this.getEmail() === null',
        constraints: [
            new Assert\NotBlank(message: 'email_or_phone_number')
        ],
    )]
    private ?PhoneNumber $phoneNumber = null;

    #[Assert\NotBlank]
    private ?ContactRoleEnum $role = null;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        if ($this->phoneNumber !== null)
        {
            return clone $this->phoneNumber;
        }

        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        if ($phoneNumber !== null)
        {
            $phoneNumber = clone $phoneNumber;
        }

        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getRole(): ?ContactRoleEnum
    {
        return $this->role;
    }

    public function setRole(?ContactRoleEnum $role): self
    {
        $this->role = $role;

        return $this;
    }
}