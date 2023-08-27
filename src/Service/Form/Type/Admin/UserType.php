<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\UserData;
use App\Model\Entity\Role;
use App\Service\Form\Type\User\BillingType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin user edit.
 */
class UserType extends AbstractType
{
    private bool $isEuBusinessDataEnabled;

    private Security $security;

    public function __construct(Security $security, bool $isEuBusinessDataEnabled)
    {
        $this->security = $security;

        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->security->isGranted('user_update_role'))
        {
            $builder
                ->add('role', EntityType::class, [
                    'class'        => Role::class,
                    'choice_label' => 'label',
                    'choices'      => $options['choices_roles'],
                    'placeholder'  => 'form.common.choice.none.female',
                    'required'     => false,
                    'label'        => 'form.admin.user.role',
                ])
            ;
        }

        if ($this->security->isGranted('user_update'))
        {
            $builder
                ->add('email', EmailType::class, [
                    'attr' => [
                        'autofocus' => 'autofocus'
                    ],
                    'label' => 'form.admin.user.email',
                ])
                ->add('billingData', BillingType::class, [
                    'label' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => UserData::class,
            'choices_roles' => [],
            'empty_data' => function (): UserData {
                return new UserData($this->isEuBusinessDataEnabled);
            },
        ]);

        $resolver->setAllowedTypes('choices_roles', ['array']);
    }
}