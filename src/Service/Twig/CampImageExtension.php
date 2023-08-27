<?php

namespace App\Service\Twig;

use App\Model\Module\CampCatalog\CampImage\CampImageFilesystemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds camp image related functions to Twig.
 */
class CampImageExtension extends AbstractExtension
{
    private CampImageFilesystemInterface $campImageFilesystem;

    public function __construct(CampImageFilesystemInterface $campImageFilesystem)
    {
        $this->campImageFilesystem = $campImageFilesystem;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('camp_image_path', [$this->campImageFilesystem, 'getFilePath']),
        ];
    }
}