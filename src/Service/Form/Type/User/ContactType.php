<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ContactData;
use App\Service\Form\Type\Common\ContactRoleType;
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
                'required'   => false,
                'label'      => 'form.user.contact.phone_number',
                'label_attr' => [
                    'class' => 'required-conditional'
                ],
            ])
            ->add('role', ContactRoleType::class, [
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'attr' => [
                    'data-fd--contact-target' => 'roleInput',
                    'data-action'             => 'fd--contact#onRoleInputChange',
                ],
                'label' => 'form.user.contact.role',
            ])
            ->add('roleOther', TextType::class, [
                'required'   => false,
                'label'      => 'form.user.contact.role_other',
                'label_attr' => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'data-fd--contact-target' => 'roleOtherRow',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactData::class,
            'attr'       => [
                'data-controller' => 'fd--contact',
            ],
        ]);
    }
}