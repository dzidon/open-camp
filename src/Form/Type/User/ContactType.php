<?php

namespace App\Form\Type\User;

use App\Form\DataTransfer\Data\User\ContactDataInterface;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User contact edit.
 */
class ContactType extends AbstractType
{
    private string $phoneNumberDefaultLocale;
    private int $phoneNumberFormat;

    public function __construct(string $phoneNumberDefaultLocale, int $phoneNumberFormat)
    {
        $this->phoneNumberDefaultLocale = $phoneNumberDefaultLocale;
        $this->phoneNumberFormat = $phoneNumberFormat;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.contact.name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.user.contact.email',
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'label'          => 'form.user.contact.phone_number',
                'default_region' => $this->phoneNumberDefaultLocale,
                'format'         => $this->phoneNumberFormat,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDataInterface::class,
        ]);
    }
}