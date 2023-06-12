<?php

namespace App\Form\Type\User;

use App\Form\DTO\User\PlainPasswordDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User plain password.
 */
class PlainPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password'
                    ],

                ],
                'first_options'  => [
                    'label' => 'form.user.plain_password.one',
                    'attr' => [
                        'autofocus' => 'autofocus',
                    ],
                ],
                'second_options' => [
                    'label' => 'form.user.plain_password.two',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlainPasswordDTO::class,
        ]);
    }
}