<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Privacy checkbox type.
 */
class PrivacyType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'checkbox_link_attr' => [
                'target' => '_blank',
            ],
            'label'               => 'form.common.privacy.label',
            'checkbox_link_label' => 'form.common.privacy.link_label',
        ]);
    }

    public function getParent(): string
    {
        return CheckboxWithUrlType::class;
    }
}