<?php

namespace App\Service\Twig;

use App\Model\Service\PaymentMethod\OfflineInstructions\OfflineInstructionRendererRegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds the ability to render offline payment instructions to Twig.
 */
class OfflinePaymentInstructionRendererExtension extends AbstractExtension
{
    private OfflineInstructionRendererRegistryInterface $offlineInstructionRendererRegistry;

    public function __construct(OfflineInstructionRendererRegistryInterface $offlineInstructionRendererRegistry)
    {
        $this->offlineInstructionRendererRegistry = $offlineInstructionRendererRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('application_offline_payment_instructions', [
                $this->offlineInstructionRendererRegistry,
                'getOfflineInstructionHtml',
            ]),
        ];
    }
}