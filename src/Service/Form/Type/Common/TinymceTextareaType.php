<?php

namespace App\Service\Form\Type\Common;

use App\Service\Theme\ThemeHttpStorageInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Tinymce WYSIWYG editor text area.
 */
class TinymceTextareaType extends AbstractType
{
    private ThemeHttpStorageInterface $themeHttpStorage;

    public function __construct(ThemeHttpStorageInterface $themeHttpStorage)
    {
        $this->themeHttpStorage = $themeHttpStorage;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $theme = $this->themeHttpStorage->getCurrentTheme();

        if ($theme === null)
        {
            $theme = $this->themeHttpStorage->getDefaultTheme();
        }

        $view->vars['attr'] = array_merge([
            'data-controller'            => 'f--tinymce',
            'data-f--tinymce-dark-value' => $theme === 'dark',
        ], $view->vars['attr']);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}