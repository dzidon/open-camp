<?php

namespace App\Form\DTO\User;

use App\Form\Type\User\PasswordChangeType;
use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * See {@link PasswordChangeType}
 */
class PasswordChangeDTO
{
    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    public ?string $email = null;

    #[Recaptcha\IsTrue]
    public ?string $captcha = null;
}