<?php

namespace App\Model\Repository;

use App\Model\Entity\FileExtension;

/**
 * File extension CRUD.
 */
interface FileExtensionRepositoryInterface
{
    /**
     * Saves a file extension.
     *
     * @param FileExtension $fileExtension
     * @param bool $flush
     * @return void
     */
    public function saveFileExtension(FileExtension $fileExtension, bool $flush): void;

    /**
     * Removes a file extension.
     *
     * @param FileExtension $fileExtension
     * @param bool $flush
     * @return void
     */
    public function removeFileExtension(FileExtension $fileExtension, bool $flush): void;

    /**
     * Creates a file extension.
     *
     * @param string $extension
     * @return FileExtension
     */
    public function createFileExtension(string $extension): FileExtension;

    /**
     * Finds file extensions attached to attachment configs.
     *
     * @return FileExtension[]
     */
    public function findForAttachmentConfigs(): array;

    /**
     * Finds one file extension.
     *
     * @param string $extension
     * @return FileExtension|null
     */
    public function findOneByExtension(string $extension): ?FileExtension;

    /**
     * Finds file extensions.
     *
     * @param string[] $extensions
     * @return FileExtension[]
     */
    public function findByExtensions(array $extensions): array;
}