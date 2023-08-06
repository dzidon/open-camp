<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\CamperDataInterface;
use App\Model\Entity\Camper;
use App\Service\Form\Type\Common\GenderType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User camper edit.
 */
class CamperType extends AbstractType
{
    private bool $isSaleCamperSiblingsEnabled;
    private bool $isNationalIdentifierEnabled;

    public function __construct(bool $isSaleCamperSiblingsEnabled, bool $isNationalIdentifierEnabled)
    {
        $this->isSaleCamperSiblingsEnabled = $isSaleCamperSiblingsEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameFirst', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.camper.name_first',
            ])
            ->add('nameLast', TextType::class, [
                'label' => 'form.user.camper.name_last',
            ])
        ;

        if ($this->isNationalIdentifierEnabled)
        {
            $builder
                ->add('nationalIdentifier', TextType::class, [
                    'label'      => 'form.user.camper.national_identifier',
                    'label_attr' => [
                        'class' => 'required'
                    ],
                    'row_attr' => [
                        'class'                                  => 'national-id-visibility',
                        'data-controller'                        => 'cv--field',
                        'data-cv--field-show-when-checked-value' => '0',
                    ],
                    'required' => false,
                ])
                ->add('isNationalIdentifierAbsent', CheckboxType::class, [
                    'label'    => 'form.user.camper.is_national_identifier_absent',
                    'attr'     => [
                        'data-controller'                    => 'cv--checkbox',
                        'data-action'                        => 'cv--checkbox#updateVisibility',
                        'data-cv--checkbox-cv--field-outlet' => '.national-id-visibility',
                    ],
                    'required' => false,
                ])
            ;
        }

        $builder
            ->add('bornAt', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'label'  => 'form.user.camper.born_at',
            ])
            ->add('gender', GenderType::class, [
                'label' => 'form.user.camper.gender',
            ])
            ->add('dietaryRestrictions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.dietary_restrictions',
            ])
            ->add('healthRestrictions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.health_restrictions',
            ])
            ->add('medication', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.medication',
            ])
        ;

        if ($this->isSaleCamperSiblingsEnabled && !empty($options['choices_siblings']))
        {
            $builder
                ->add('siblings', EntityType::class, [
                    'class'        => Camper::class,
                    'choice_label' => function (Camper $camper) {
                        return $camper->getNameFirst() . ' ' . $camper->getNameLast();
                    },
                    'choices'  => $options['choices_siblings'],
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                    'label'    => 'form.user.camper.siblings',
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CamperDataInterface::class,
        ]);

        if ($this->isSaleCamperSiblingsEnabled)
        {
            $resolver->setDefault('choices_siblings', []);
            $resolver->setAllowedTypes('choices_siblings', ['array']);
        }
    }
}