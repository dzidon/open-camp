<?php

namespace App\Service\Data\Factory\ApplicationAttachment;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\User\ApplicationAttachmentsData;
use App\Library\Data\User\ApplicationAttachmentsUploadLaterData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAttachment;

/**
 * @inheritDoc
 */
class ApplicationAttachmentsUploadLaterDataFactory implements ApplicationAttachmentsUploadLaterDataFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createApplicationAttachmentsUploadLaterData(Application $application): ApplicationAttachmentsUploadLaterData
    {
        $applicationAttachmentsUploadLaterData = new ApplicationAttachmentsUploadLaterData();
        $applicationCampers = $application->getApplicationCampers();
        $applicationAttachments = $application->getApplicationAttachments();

        // whole application
        $applicationAttachmentsData = new ApplicationAttachmentsData($application, null);

        foreach ($applicationAttachments as $applicationAttachment)
        {
            if (!$applicationAttachment->canBeUploadedLater())
            {
                continue;
            }

            $applicationAttachmentData = $this->createApplicationAttachmentData($applicationAttachment);
            $applicationAttachmentsData->addApplicationAttachmentsDatum($applicationAttachmentData);
        }

        if (!empty($applicationAttachmentsData->getApplicationAttachmentsData()))
        {
            $applicationAttachmentsUploadLaterData->addApplicationAttachmentsDatum($applicationAttachmentsData);
        }

        // per camper
        foreach ($applicationCampers as $applicationCamper)
        {
            $applicationAttachmentsData = new ApplicationAttachmentsData(null, $applicationCamper);
            $applicationCamperAttachments = $applicationCamper->getApplicationAttachments();

            foreach ($applicationCamperAttachments as $applicationCamperAttachment)
            {
                if (!$applicationCamperAttachment->canBeUploadedLater())
                {
                    continue;
                }

                $applicationAttachmentData = $this->createApplicationAttachmentData($applicationCamperAttachment);
                $applicationAttachmentsData->addApplicationAttachmentsDatum($applicationAttachmentData);
            }

            if (!empty($applicationAttachmentsData->getApplicationAttachmentsData()))
            {
                $applicationAttachmentsUploadLaterData->addApplicationAttachmentsDatum($applicationAttachmentsData);
            }
        }

        return $applicationAttachmentsUploadLaterData;
    }

    private function createApplicationAttachmentData(ApplicationAttachment $applicationAttachment): ApplicationAttachmentData
    {
        return new ApplicationAttachmentData(
            $applicationAttachment->getMaxSize(),
            $applicationAttachment->getRequiredType(),
            $applicationAttachment->getExtensions(),
            $applicationAttachment->isAlreadyUploaded(),
            $applicationAttachment->getPriority(),
            $applicationAttachment->getLabel(),
            $applicationAttachment->getHelp(),
            true
        );
    }
}