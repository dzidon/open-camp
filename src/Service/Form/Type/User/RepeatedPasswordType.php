<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\PlainPasswordData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User plain password repeated type.
 */
class RepeatedPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type'    => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password'
                    ],
                ],
                'first_options'  => [
                    'label' => 'form.user.repeated_password.first',
                    'attr'  => [
                        'autofocus' => 'autofocus',
                    ],
                ],
                'second_options' => [
                    'label' => 'form.user.repeated_password.second',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlainPasswordData::class,
        ]);
    }
}