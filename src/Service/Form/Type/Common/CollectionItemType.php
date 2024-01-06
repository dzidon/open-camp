<?php

namespace App\Service\Form\Type\Common;

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
                    'data-toggle'              => 'modal',
                    'data-target'              => '#fc-removal-modal',
                    'data-controller'          => 'fc--rem-prep',
                    'data-fc--rem-prep-target' => 'button',
                    'data-action'              => 'fc--rem-prep#prepareItemForRemoval',
                ],
                'label'    => $options['remove_button_label'],
                'priority' => $options['remove_button_priority'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'remove_button'          => false,
            'remove_button_priority' => -100,
            'remove_button_label'    => 'form.common.collection.remove',
            'row_attr'               => [
                'data-fc--wrap-target' => 'item',
                'class'                => 'm-0',
            ],
        ]);

        $resolver->setAllowedTypes('remove_button', 'bool');
        $resolver->setAllowedTypes('remove_button_priority', 'int');
        $resolver->setAllowedTypes('remove_button_label', 'string');
    }
}