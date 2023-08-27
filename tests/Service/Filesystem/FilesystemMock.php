<?php

namespace App\Tests\Service\Filesystem;

use Symfony\Component\Filesystem\Filesystem;
use Traversable;

/**
 * Filesystem mock used for testing. Overrides the original Filesystem functionality.
 */
class FilesystemMock extends Filesystem
{
    private array $removedFiles = [];

    public function getRemovedFiles(): array
    {
        return $this->removedFiles;
    }

    public function remove(string|iterable $files): void
    {
        if ($files instanceof Traversable)
        {
            $files = iterator_to_array($files, false);
        }
        else if (!is_array($files))
        {
            $files = [$files];
        }

        foreach ($files as $file)
        {
            $this->removedFiles[] = $file;
        }
    }
}