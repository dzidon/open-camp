<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Checkbox that has a link in its label.
 */
class CheckboxWithUrlType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['checkbox_link_url'] = $options['checkbox_link_url'];
        $view->vars['checkbox_link_label'] = $options['checkbox_link_label'];
        $view->vars['checkbox_link_attr'] = $options['checkbox_link_attr'];
        $view->vars['checkbox_link_translation_parameters'] = $options['checkbox_link_translation_parameters'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'checkbox_link_url', 'checkbox_link_label',
            'checkbox_link_attr', 'checkbox_link_translation_parameters'
        ]);

        $resolver->setRequired([
            'checkbox_link_url', 'checkbox_link_label',
            'checkbox_link_attr', 'checkbox_link_translation_parameters'
        ]);

        $resolver->setDefaults([
            'label_html'                           => true,
            'checkbox_link_url'                    => '#',
            'checkbox_link_attr'                   => [],
            'checkbox_link_translation_parameters' => [],
        ]);

        $resolver->setAllowedTypes('checkbox_link_url', 'string');
        $resolver->setAllowedTypes('checkbox_link_label', 'string');
        $resolver->setAllowedTypes('checkbox_link_attr', 'array');
        $resolver->setAllowedTypes('checkbox_link_translation_parameters', 'array');
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}