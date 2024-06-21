<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Tinymce WYSIWYG editor text area.
 */
class TinymceTextareaType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge([
            'data-controller'            => 'f--tinymce',
            'data-f--tinymce-dark-value' => false,
        ], $view->vars['attr']);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}