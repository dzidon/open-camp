<?php

namespace App\Service\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Animates the FileType.
 */
class FileTypeAnimationExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge([
            'data-controller' => 'f--file-type',
        ], $view->vars['attr']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FileType::class];
    }
}