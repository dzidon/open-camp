<?php

namespace App\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form types that serve as collection items can extend this type. Can add a "Remove" button at the end of the form
 * and sets up some Stimulus attributes so that a confirmation modal window can be used.
 *
 * Note: To extend this form type, use {@link FormTypeInterface::getParent()}
 */
class CollectionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['remove_button'])
        {
            return;
        }

        $builder
            ->add('removeItem', ButtonType::class, [
                'attr' => [
                    'class'                    => 'btn btn-danger',
                    'data-bs-toggle'           => 'modal',
                    'data-bs-target'           => '#fc-removal-modal',
                    'data-controller'          => 'fc--rem-prep',
                    'data-fc--rem-prep-target' => 'button',
                    'data-action'              => 'fc--rem-prep#prepareItemForRemoval',
                ],
                'label'    => 'Remove',
                'priority' => -100,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'remove_button' => false,
            'row_attr' => [
                'data-fc--wrap-target' => 'item',
            ],
        ]);

        $resolver->setAllowedTypes('remove_button', 'bool');
    }
}