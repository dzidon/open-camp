<?php

namespace App\Form\DataTransfer\Data\User;

use App\Form\Type\User\ContactType;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link ContactType}
 */
class ContactData implements ContactDataInterface
{
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private string $name = '';

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email = '';

    #[AssertPhoneNumber]
    private PhoneNumber $phoneNumber;

    public function __construct()
    {
        $this->phoneNumber = new PhoneNumber();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = (string) $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = (string) $email;

        return $this;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        if ($phoneNumber === null)
        {
            $phoneNumber = new PhoneNumber();
        }

        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}