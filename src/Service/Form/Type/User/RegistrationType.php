<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\RegistrationData;
use App\Service\Form\Type\Common\PrivacyType;
use App\Service\Form\Type\Common\TermsOfUseType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User registration.
 */
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.registration.email',
            ])
            ->add('captcha', EWZRecaptchaType::class)
            ->add('privacy', PrivacyType::class)
            ->add('terms', TermsOfUseType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationData::class,
        ]);
    }
}