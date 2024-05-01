<?php

namespace App\Service\Form\Type\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Privacy checkbox type.
 */
class PrivacyType extends AbstractType
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $linkUrl = $this->urlGenerator->generate('user_privacy');

        $resolver->setDefaults([
            'checkbox_link_attr' => [
                'target' => '_blank',
            ],
            'label'               => 'form.common.privacy.label',
            'checkbox_link_label' => 'form.common.privacy.link_label',
            'checkbox_link_url'   => $linkUrl,
        ]);
    }

    public function getParent(): string
    {
        return CheckboxWithUrlType::class;
    }
}