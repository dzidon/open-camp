<?php

namespace App\Model\Service\ApplicationAttachment;

use App\Library\Data\User\ApplicationAttachmentsUploadLaterData;

/**
 * Uploads attachments for a completed application.
 */
interface ApplicationAttachmentsUploadLaterInterface
{
    /**
     * Uploads attachments for a completed application.
     *
     * @param ApplicationAttachmentsUploadLaterData $data
     * @return void
     */
    public function uploadApplicationAttachments(ApplicationAttachmentsUploadLaterData $data): void;
}