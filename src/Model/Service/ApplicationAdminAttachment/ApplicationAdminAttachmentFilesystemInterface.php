<?php

namespace App\Model\Service\ApplicationAdminAttachment;

use App\Model\Entity\ApplicationAdminAttachment;

/**
 * Helper service for admin attachment files.
 */
interface ApplicationAdminAttachmentFilesystemInterface
{
    /**
     * Returns the contents of the given application admin attachment.
     *
     * @param ApplicationAdminAttachment $applicationAdminAttachment
     * @return string|null
     */
    public function getFileContents(ApplicationAdminAttachment $applicationAdminAttachment): ?string;

    /**
     * Removes the file of the given application admin attachment.
     *
     * @param ApplicationAdminAttachment $applicationAdminAttachment
     * @return void
     */
    public function removeFile(ApplicationAdminAttachment $applicationAdminAttachment): void;
}