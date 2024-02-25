<?php

namespace App\Model\Service\PaymentMethod\OfflineInstructions;

use App\Model\Entity\Application;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Renders HTML instructions for the given application & payment method.
 */
interface OfflineInstructionRendererInterface
{
    /**
     * Returns true if this renderer supports the given application.
     *
     * @param Application $application
     * @return bool
     */
    public function supports(Application $application): bool;

    /**
     * Returns the HTML.
     *
     * @param Application $application
     * @param array $options
     * @return string
     */
    public function getOfflineInstructionHtml(Application $application, array $options = []): string;

    /**
     * Lets us add extra data to the HTML content.
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void;
}