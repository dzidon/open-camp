<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationAttachment;

interface ApplicationAttachmentRepositoryInterface
{
    /**
     * Saves an application attachment.
     *
     * @param ApplicationAttachment $applicationAttachment
     * @param bool $flush
     * @return void
     */
    public function saveApplicationAttachment(ApplicationAttachment $applicationAttachment, bool $flush): void;

    /**
     * Removes an application attachment.
     *
     * @param ApplicationAttachment $applicationAttachment
     * @param bool $flush
     * @return void
     */
    public function removeApplicationAttachment(ApplicationAttachment $applicationAttachment, bool $flush): void;
}