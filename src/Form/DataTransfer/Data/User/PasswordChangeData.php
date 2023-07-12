<?php

namespace App\Form\DataTransfer\Data\User;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class PasswordChangeData implements PasswordChangeDataInterface
{
    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[Recaptcha\IsTrue]
    private ?string $captcha = null;

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
}