<?php

namespace App\Service\Filesystem;

/**
 * @inheritDoc
 */
class FilePathTransformer implements FilePathTransformerInterface
{
    private string $kernelProjectDir;

    public function __construct(string $kernelProjectDir)
    {
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @inheritDoc
     */
    public function kernelPathToRelativeUrl(string $filePath): string
    {
        $stringToRemove = $this->kernelProjectDir . '/public';

        if (!str_starts_with($filePath, $stringToRemove))
        {
            return $filePath;
        }

        return ltrim($filePath, $stringToRemove);
    }
}