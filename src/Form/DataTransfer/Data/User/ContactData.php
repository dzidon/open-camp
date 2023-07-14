<?php

namespace App\Form\DataTransfer\Data\User;

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
    private ?string $name = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[AssertPhoneNumber]
    #[Assert\NotBlank]
    private ?PhoneNumber $phoneNumber = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
}