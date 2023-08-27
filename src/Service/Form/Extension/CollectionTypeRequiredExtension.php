<?php

namespace App\Service\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Collection type extension that suppresses the required option.
 */
class CollectionTypeRequiredExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['suppress_required_rendering'] = $options['suppress_required_rendering'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['suppress_required_rendering' => false]);
        $resolver->setAllowedTypes('suppress_required_rendering', 'bool');
    }

    public static function getExtendedTypes(): iterable
    {
        return [CollectionType::class];
    }
}