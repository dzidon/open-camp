<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ProfileData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin profile edit.
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameFirst', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.profile.name_first',
            ])
            ->add('nameLast', TextType::class, [
                'required' => false,
                'label'    => 'form.admin.profile.name_last',
            ])
            ->add('bornAt', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.profile.born_at',
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.profile.bio',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'multiple' => false,
                'label'    => 'form.admin.profile.image',
                'row_attr' => [
                    'class'                                   => 'user-image',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '0',
                ],
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
                        'label'    => 'form.admin.profile.remove_image',
                        'attr'     => [
                            'data-controller'                      => 'cv--checkbox',
                            'data-action'                          => 'cv--checkbox#updateVisibility',
                            'data-cv--checkbox-cv--content-outlet' => '.user-image',
                        ],
                    ])
                ;
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ProfileData::class,
            'block_prefix' => 'admin_profile',
        ]);
    }
}