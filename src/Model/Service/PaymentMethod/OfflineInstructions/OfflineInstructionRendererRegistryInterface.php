<?php

namespace App\Model\Service\PaymentMethod\OfflineInstructions;

use App\Model\Entity\Application;

/**
 * Renders HTML with instructions on how to pay offline.
 */
interface OfflineInstructionRendererRegistryInterface
{
    /**
     * Registers a renderer.
     *
     * @param OfflineInstructionRendererInterface $offlineInstructionRenderer
     * @return void
     */
    public function registerOfflineInstructionRenderer(OfflineInstructionRendererInterface $offlineInstructionRenderer): void;

    /**
     * Finds a renderer that supports the given application and returns the HTML.
     *
     * @param Application $application
     * @param array $options
     * @return string
     */
    public function getOfflineInstructionHtml(Application $application, array $options = []): string;
}