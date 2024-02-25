<?php

namespace App\Model\Service\PaymentMethod\OfflineInstructions;

use App\Model\Entity\Application;
use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @inheritDoc
 */
class OfflineInstructionRendererRegistry implements OfflineInstructionRendererRegistryInterface
{
    /** @var OfflineInstructionRendererInterface[] */
    public array $offlineInstructionRenderers = [];

    /**
     * @inheritDoc
     */
    public function registerOfflineInstructionRenderer(OfflineInstructionRendererInterface $offlineInstructionRenderer): void
    {
        if (in_array($offlineInstructionRenderer, $this->offlineInstructionRenderers))
        {
            return;
        }

        $this->offlineInstructionRenderers[] = $offlineInstructionRenderer;
    }

    /**
     * @inheritDoc
     */
    public function getOfflineInstructionHtml(Application $application, array $options = []): string
    {
        $paymentMethod = $application->getPaymentMethod();

        if ($paymentMethod === null || $paymentMethod->isOnline())
        {
            throw new LogicException(
                sprintf('Application passed to "%s" must have an offline payment method.', __METHOD__)
            );
        }

        foreach ($this->offlineInstructionRenderers as $offlineInstructionRenderer)
        {
            if ($offlineInstructionRenderer->supports($application))
            {
                $resolver = new OptionsResolver();
                $offlineInstructionRenderer->configureOptions($resolver);
                $resolvedOptions = $resolver->resolve($options);

                return $offlineInstructionRenderer->getOfflineInstructionHtml($application, $resolvedOptions);
            }
        }

        $paymentMethodName = $paymentMethod->getName();

        throw new LogicException(
            sprintf('Method %s cannot be used. There is no offline instructions class that supports %s.', __METHOD__, $paymentMethodName)
        );
    }
}