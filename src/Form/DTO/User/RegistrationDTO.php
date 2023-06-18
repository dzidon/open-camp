<?php

namespace App\Form\DTO\User;

use App\Form\Type\User\RegistrationType;
use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * See {@link RegistrationType}
 */
class RegistrationDTO
{
    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    public ?string $email = null;

    #[Recaptcha\IsTrue]
    public ?string $captcha = null;

    #[Assert\IsTrue]
    public bool $privacy = false;

    #[Assert\IsTrue]
    public bool $terms = false;
}