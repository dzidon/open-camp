<?php

namespace App\Service\Form\Extension;

use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Sets the default label for Captcha type.
 */
class CaptchaTypeLabelExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'form.common.captcha.label',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [EWZRecaptchaType::class];
    }
}