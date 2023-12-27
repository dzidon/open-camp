<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Tinymce WYSIWYG editor text area.
 */
class TinymceTextareaType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'data-controller' => 'f--tinymce',
            ],
        ]);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}