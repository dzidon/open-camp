<?php

namespace App\Library\Data\User;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

class ContactUsData
{
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[AssertPhoneNumber]
    #[Assert\NotBlank]
    private ?PhoneNumber $phoneNumber = null;

    #[Assert\Length(max: 2000)]
    #[Assert\NotBlank]
    private ?string $message = null;

    #[Recaptcha\IsTrue]
    private ?string $captcha = null;

    #[Assert\IsTrue]
    private bool $privacy = false;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCaptcha(): ?string
    {
        return $this->captcha;
    }

    public function setCaptcha(?string $captcha): void
    {
        $this->captcha = $captcha;
    }

    public function isPrivacy(): bool
    {
        return $this->privacy;
    }

    public function setPrivacy(bool $privacy): self
    {
        $this->privacy = $privacy;

        return $this;
    }
}