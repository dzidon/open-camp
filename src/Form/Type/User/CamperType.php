<?php

namespace App\Form\Type\User;

use App\Form\DataTransfer\Data\User\CamperDataInterface;
use App\Form\Type\Common\GenderType;
use App\Model\Entity\Camper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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

    public function __construct(bool $isSaleCamperSiblingsEnabled)
    {
        $this->isSaleCamperSiblingsEnabled = $isSaleCamperSiblingsEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.user.camper.name',
            ])
            ->add('gender', GenderType::class, [
                'label' => 'form.user.camper.gender',
            ])
            ->add('bornAt', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'label'  => 'form.user.camper.born_at',
            ])
            ->add('dietaryRestrictions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.dietary_restrictions',
            ])
            ->add('healthRestrictions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.user.camper.health_restrictions',
            ])
        ;

        if ($this->isSaleCamperSiblingsEnabled && !empty($options['choices_siblings']))
        {
            $builder
                ->add('siblings', EntityType::class, [
                    'class'        => Camper::class,
                    'choice_label' => function (Camper $camper) {
                        return $camper->getName();
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