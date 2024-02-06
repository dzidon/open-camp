<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Terms of use checkbox type.
 */
class TermsOfUseType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'checkbox_link_attr' => [
                'target' => '_blank',
            ],
            'label'               => 'form.common.terms.label',
            'checkbox_link_label' => 'form.common.terms.link_label',
        ]);
    }

    public function getParent(): string
    {
        return CheckboxWithUrlType::class;
    }
}