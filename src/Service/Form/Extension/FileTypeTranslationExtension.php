<?php

namespace App\Service\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Extends FileType, so it has a translated "Browse" label.
 */
class FileTypeTranslationExtension extends AbstractTypeExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['label_attr'] = array_merge([
            'data-browse' => $this->translator->trans('form.common.file.browse'),
        ], $view->vars['label_attr']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FileType::class];
    }
}