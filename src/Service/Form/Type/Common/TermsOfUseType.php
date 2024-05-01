<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Terms of use checkbox type.
 */
class TermsOfUseType extends AbstractType
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $linkUrl = $this->urlGenerator->generate('user_terms_of_use');

        $resolver->setDefaults([
            'checkbox_link_attr' => [
                'target' => '_blank',
            ],
            'label'               => 'form.common.terms.label',
            'checkbox_link_label' => 'form.common.terms.link_label',
            'checkbox_link_url'   => $linkUrl,
        ]);
    }

    public function getParent(): string
    {
        return CheckboxWithUrlType::class;
    }
}