<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\CamperData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User camper edit.
 */
class CamperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var null|CamperData $camperData */
            $camperData = $event->getData();
            $form = $event->getForm();

            if ($camperData === null)
            {
                return;
            }

            if (!$camperData->isNationalIdentifierEnabled())
            {
                return;
            }

            $form
                ->add('nationalIdentifier', TextType::class, [
                    'label'      => 'form.user.camper.national_identifier',
                    'label_attr' => [
                        'class' => 'required'
                    ],
                    'row_attr' => [
                        'class'                                   => 'national-id-visibility',
                        'data-controller'                         => 'cv--content',
                        'data-cv--content-show-when-chosen-value' => '0',
                    ],
                    'required' => false,
                    'priority' => 700,
                ])
                ->add('isNationalIdentifierAbsent', CheckboxType::class, [
                    'label' => 'form.user.camper.is_national_identifier_absent',
                    'attr'  => [
                        'data-controller'                      => 'cv--checkbox',
                        'data-action'                          => 'cv--checkbox#updateVisibility',
                        'data-cv--checkbox-cv--content-outlet' => '.national-id-visibility',
                    ],
                    'required' => false,
                    'priority' => 600,
                ])
            ;
        });

        $builder
            ->add('nameFirst', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label'    => 'form.user.camper.name_first',
                'priority' => 900,
            ])
            ->add('nameLast', TextType::class, [
                'label'    => 'form.user.camper.name_last',
                'priority' => 800,
            ])
            ->add('bornAt', DateType::class, [
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.user.camper.born_at',
                'priority' => 500,
            ])
            ->add('gender', GenderChildishType::class, [
                'label'    => 'form.user.camper.gender',
                'priority' => 400,
            ])
            ->add('dietaryRestrictions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.dietary_restrictions',
                'priority' => 300,
            ])
            ->add('healthRestrictions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.health_restrictions',
                'priority' => 200,
            ])
            ->add('medication', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.medication',
                'priority' => 100,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => CamperData::class,
            'block_prefix' => 'common_camper',
        ]);
    }
}