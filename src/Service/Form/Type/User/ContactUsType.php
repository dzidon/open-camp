<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ContactUsData;
use App\Service\Form\Type\Common\PrivacyType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User contact us form.
 */
class ContactUsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.contact_us.name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.user.contact_us.email',
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'label' => 'form.user.contact_us.phone_number',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.user.contact_us.message',
            ])
            ->add('captcha', EWZRecaptchaType::class)
            ->add('privacy', PrivacyType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactUsData::class,
        ]);
    }
}