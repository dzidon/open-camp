<?php

namespace App\Library\Data\User;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class RegistrationData implements RegistrationDataInterface
{
    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[Recaptcha\IsTrue]
    private ?string $captcha = null;

    #[Assert\IsTrue]
    private bool $privacy = false;

    #[Assert\IsTrue]
    private bool $terms = false;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function isTerms(): bool
    {
        return $this->terms;
    }

    public function setTerms(bool $terms): self
    {
        $this->terms = $terms;

        return $this;
    }
}