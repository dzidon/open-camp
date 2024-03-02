<?php

namespace App\Tests\Library\Http\File;

use Symfony\Component\HttpFoundation\File\File;

/**
 * File mock for testing.
 */
class FileMock extends File
{
    private ?string $guessedExtension;
    private ?string $movedDirectory = null;
    private ?string $movedName = null;
    private string $content;

    public function __construct(?string $guessedExtension = null, string $path = '', string $content = '')
    {
        parent::__construct($path, false);

        $this->guessedExtension = $guessedExtension;
        $this->content = $content;
    }

    public function move(string $directory, string $name = null): self
    {
        $this->movedDirectory = $directory;
        $this->movedName = $name;

        return $this;
    }

    public function guessExtension(): ?string
    {
        return $this->guessedExtension;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMovedDirectory(): ?string
    {
        return $this->movedDirectory;
    }

    public function getMovedName(): ?string
    {
        return $this->movedName;
    }
}