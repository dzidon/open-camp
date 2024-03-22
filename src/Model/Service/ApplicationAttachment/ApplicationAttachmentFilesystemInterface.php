<?php

namespace App\Model\Service\ApplicationAttachment;

use App\Model\Entity\ApplicationAttachment;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Helper service for application attachment files.
 */
interface ApplicationAttachmentFilesystemInterface
{
    /**
     * Returns the contents of the given application attachment.
     *
     * @param ApplicationAttachment $applicationAttachment
     * @return string|null
     */
    public function getFileContents(ApplicationAttachment $applicationAttachment): ?string;

    /**
     * Uploads the given file and attaches it to the given application attachment.
     *
     * @param File $file
     * @param ApplicationAttachment $applicationAttachment
     * @return void
     */
    public function uploadFile(File $file, ApplicationAttachment $applicationAttachment): void;

    /**
     * Removes the file of the given application attachment.
     *
     * @param ApplicationAttachment $applicationAttachment
     * @return void
     */
    public function removeFile(ApplicationAttachment $applicationAttachment): void;
}