<?php

namespace App\Form\Type\User;

use App\Form\DTO\User\PasswordChangeDTO;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User password change.
 */
class PasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.password_change.email',
            ])
            ->add('captcha', EWZRecaptchaType::class, [
                'label' => 'form.user.password_change.captcha',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordChangeDTO::class,
        ]);
    }
}