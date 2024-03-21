<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ProfileData;
use App\Library\Data\Admin\UserData;
use App\Model\Entity\Role;
use App\Service\Form\Type\User\BillingType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin user edit.
 */
class UserType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->security->isGranted('user_update'))
        {
            $builder
                ->add('email', EmailType::class, [
                    'attr' => [
                        'autofocus' => 'autofocus'
                    ],
                    'label'    => 'form.admin.user.email',
                    'priority' => 5000,
                ])
                ->add('billingData', BillingType::class, [
                    'label'    => false,
                    'priority' => 4900,
                ])
                ->add('urlName', TextType::class, [
                    'required' => false,
                    'label'    => 'form.admin.user.url_name',
                    'priority' => 4800,
                ])
                ->add('guidePriority', IntegerType::class, [
                    'label'    => 'form.admin.user.guide_priority',
                    'priority' => 4700,
                ])
                ->add('bornAt', DateType::class, [
                    'required' => false,
                    'widget'   => 'single_text',
                    'input'    => 'datetime_immutable',
                    'label'    => 'form.admin.user.born_at',
                    'priority' => 4600,
                ])
                ->add('bio', TextareaType::class, [
                    'required' => false,
                    'label'    => 'form.admin.user.bio',
                    'priority' => 4500,
                ])
                ->add('image', FileType::class, [
                    'required' => false,
                    'multiple' => false,
                    'label'    => 'form.admin.user.image',
                    'row_attr' => [
                        'class'                                   => 'user-image',
                        'data-controller'                         => 'cv--content',
                        'data-cv--content-show-when-chosen-value' => '0',
                    ],
                    'priority' => 4400,
                ])
            ;

            $builder->addEventListener(FormEvents::PRE_SET_DATA,
                function (FormEvent $event): void
                {
                    /** @var ProfileData $data */
                    $data = $event->getData();
                    $user = $data->getUser();
                    $form = $event->getForm();

                    if ($user === null || $user->getImageExtension() === null)
                    {
                        return;
                    }

                    $form
                        ->add('removeImage', CheckboxType::class, [
                            'required' => false,
                            'label'    => 'form.admin.user.remove_image',
                            'attr'     => [
                                'data-controller'                      => 'cv--checkbox',
                                'data-action'                          => 'cv--checkbox#updateVisibility',
                                'data-cv--checkbox-cv--content-outlet' => '.user-image',
                            ],
                            'priority' => 4300,
                        ])
                    ;
                }
            );
        }

        if ($this->security->isGranted('user_role_update'))
        {
            $builder
                ->add('role', EntityType::class, [
                    'class'        => Role::class,
                    'choice_label' => 'label',
                    'choices'      => $options['choices_roles'],
                    'placeholder'  => 'form.common.choice.none.female',
                    'required'     => false,
                    'label'        => 'form.admin.user.role',
                    'priority'     => 4200,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => UserData::class,
            'block_prefix'  => 'admin_user',
            'choices_roles' => [],
        ]);

        $resolver->setAllowedTypes('choices_roles', ['array']);
    }
}