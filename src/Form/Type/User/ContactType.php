<?php

namespace App\Form\Type\User;

use App\Form\DataTransfer\Data\User\ContactDataInterface;
use App\Form\Type\Common\ContactRoleType;
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
            ->add('nameFirst', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.contact.name_first',
            ])
            ->add('nameLast', TextType::class, [
                'label' => 'form.user.contact.name_last',
            ])
            ->add('email', EmailType::class, [
                'required'   => false,
                'label'      => 'form.user.contact.email',
                'label_attr' => [
                    'class' => 'required-conditional'
                ],
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'required'       => false,
                'label'          => 'form.user.contact.phone_number',
                'default_region' => $this->phoneNumberDefaultLocale,
                'format'         => $this->phoneNumberFormat,
                'label_attr'     => [
                    'class' => 'required-conditional'
                ],
            ])
            ->add('role', ContactRoleType::class, [
                'label' => 'form.user.contact.role',
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