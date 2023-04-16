<?php

namespace App\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Button that adds items to a form collection using Stimulus.
 */
class CollectionAddItemButtonType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge([
            'data-controller'                    => 'fc--add',
            'data-action'                        => 'fc--add#addItem',
            'data-fc--add-collection-name-value' => $options['collection_name'],
            'data-fc--add-form-name-value'       => $form->getParent()->getName(),
        ], $view->vars['attr']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('collection_name')
            ->setAllowedTypes('collection_name', 'string')
            ->setRequired('collection_name')
        ;
    }

    public function getParent(): string
    {
        return ButtonType::class;
    }
}