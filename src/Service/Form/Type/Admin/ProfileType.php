<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ProfileData;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin profile edit.
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('leaderPhoneNumber', PhoneNumberType::class, [
                'required' => false,
                'label'    => 'form.admin.profile.leader_phone_number',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileData::class,
        ]);
    }
}