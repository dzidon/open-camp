<?php

namespace App\Library\Data\User;

use App\Library\Constraint\NotBlankContactEmail;
use App\Library\Constraint\NotBlankContactPhoneNumber;
use App\Model\Enum\Entity\ContactRoleEnum;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

#[NotBlankContactEmail]
#[NotBlankContactPhoneNumber]
class ContactData
{
    private bool $isEmailMandatory;

    private bool $isPhoneNumberMandatory;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameFirst = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameLast = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    private ?string $email = null;

    #[AssertPhoneNumber]
    private ?PhoneNumber $phoneNumber = null;

    #[Assert\NotBlank]
    private ?ContactRoleEnum $role = null;

    #[Assert\When(
        expression: 'this.getRole() === enum("App\\\Model\\\Enum\\\Entity\\\ContactRoleEnum::OTHER")',
        constraints: [
            new Assert\Length(max: 255),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $roleOther = null;

    public function __construct(bool $isEmailMandatory, bool $isPhoneNumberMandatory)
    {
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
    }

    public function isEmailMandatory(): bool
    {
        return $this->isEmailMandatory;
    }

    public function isPhoneNumberMandatory(): bool
    {
        return $this->isPhoneNumberMandatory;
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

    public function getRoleOther(): ?string
    {
        return $this->roleOther;
    }

    public function setRoleOther(?string $roleOther): self
    {
        $this->roleOther = $roleOther;

        return $this;
    }
}