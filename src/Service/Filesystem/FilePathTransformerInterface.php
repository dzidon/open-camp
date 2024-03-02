<?php

namespace App\Service\Filesystem;

/**
 * Transforms file paths.
 */
interface FilePathTransformerInterface
{
    /**
     * Transforms a path that contains kernel path to a relative url.
     *
     * @param string $filePath
     * @return string
     */
    public function kernelPathToRelativeUrl(string $filePath): string;
}