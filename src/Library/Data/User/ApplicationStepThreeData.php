<?php

namespace App\Library\Data\User;

use App\Model\Entity\User;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationStepThreeData
{
    private ?User $user;

    #[Assert\When(
        expression: 'this.getUser() === null',
        constraints: [
            new Recaptcha\IsTrue(),
        ],
    )]
    private ?string $captcha = null;

    #[Assert\IsTrue]
    private bool $privacy = false;

    #[Assert\IsTrue]
    private bool $terms = false;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
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