<?php

namespace App\Service\Data\Factory\ApplicationAttachment;

use App\Library\Data\User\ApplicationAttachmentsUploadLaterData;
use App\Model\Entity\Application;

/**
 * Creates a DTO that allows users to upload attachments after their application got completed.
 */
interface ApplicationAttachmentsUploadLaterDataFactoryInterface
{
    /**
     * Creates a DTO that allows users to upload attachments after their application got completed.
     *
     * @param Application $application
     * @return ApplicationAttachmentsUploadLaterData
     */
    public function createApplicationAttachmentsUploadLaterData(Application $application): ApplicationAttachmentsUploadLaterData;
}