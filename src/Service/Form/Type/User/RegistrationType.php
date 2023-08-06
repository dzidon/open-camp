<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\RegistrationDataInterface;
use App\Service\Form\Type\Common\CheckboxWithUrlType;
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
            ->add('captcha', EWZRecaptchaType::class, [
                'label' => 'form.user.registration.captcha',
            ])
            ->add('privacy', CheckboxWithUrlType::class, [
                'checkbox_link_attr' => [
                    'target' => '_blank',
                ],
                'label' => 'form.user.registration.privacy',
                'checkbox_link_label' => 'form.user.registration.privacy_link',
            ])
            ->add('terms', CheckboxWithUrlType::class, [
                'checkbox_link_attr' => [
                    'target' => '_blank',
                ],
                'label' => 'form.user.registration.terms',
                'checkbox_link_label' => 'form.user.registration.terms_link',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationDataInterface::class,
        ]);
    }
}