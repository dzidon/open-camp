<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Constraint\Compound\FormFieldChoiceItemRequirements;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin form field dropdown choice editing.
 */
class FormFieldChoiceItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $constraints = [];

        if ($options['enable_validation'])
        {
            $constraints = [
                new FormFieldChoiceItemRequirements(),
            ];
        }

        $builder
            ->add('value', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'constraints' => $constraints,
                'empty_data'  => '',
                'label'       => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('enable_validation', false);
        $resolver->setAllowedTypes('enable_validation', ['bool']);
    }

    public function getParent(): string
    {
        return CollectionItemType::class;
    }
}