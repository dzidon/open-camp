<?php

namespace App\Model\Service\PaymentMethod\OfflineInstructions;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Helper for classes that provide offline payment instructions.
 */
abstract class AbstractOfflineInstructionRenderer implements OfflineInstructionRendererInterface
{
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}