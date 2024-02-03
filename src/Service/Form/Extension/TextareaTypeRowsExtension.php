<?php

namespace App\Service\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Sets the default number of rows for a text area.
 */
class TextareaTypeRowsExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge([
            'rows' => 5,
        ], $view->vars['attr']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class];
    }
}