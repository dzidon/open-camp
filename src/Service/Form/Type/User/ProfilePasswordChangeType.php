<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ProfilePasswordChangeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User profile password change.
 */
class ProfilePasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.profile_password_change.current_password',
            ])
            ->add('newPasswordData', RepeatedPasswordType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilePasswordChangeData::class,
        ]);
    }
}