<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Extends CollectionType so it can be used with Stimulus.
 */
class CollectionTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = array_merge([
            'data-controller'                     => 'fc--wrap',
            'data-fc--wrap-target'                => 'fields',
            'data-fc--wrap-collection-name-value' => $view->vars['name'],
            'data-fc--wrap-form-name-value'       => $form->getParent()->getName(),
            'data-action' => 'fc--add:addItem@window->fc--wrap#addItem 
                              fc--rem-prep:resetPreparedItem@window->fc--wrap#resetPreparedItem
                              fc--rem-prep:prepareItemForRemoval->fc--wrap#prepareItemForRemoval
                              fc--rem-mod:removePreparedItem@window->fc--wrap#removePreparedItem',
        ], $view->vars['attr']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CollectionType::class];
    }
}