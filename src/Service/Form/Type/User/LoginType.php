<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\LoginData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User login.
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', EmailType::class, [
                'property_path' => 'email',
                'attr'          => [
                    'autofocus' => 'autofocus'
                ],
                'label'         => 'form.user.login.email',
            ])
            ->add('_password', PasswordType::class, [
                'property_path' => 'password',
                'label'         => 'form.user.login.password',
            ])
            ->add('_remember_me', CheckboxType::class, [
                'property_path' => 'rememberMe',
                'label'         => 'form.user.login.remember_me',
                'required'      => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => LoginData::class,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'authenticate',
        ]);
    }
}