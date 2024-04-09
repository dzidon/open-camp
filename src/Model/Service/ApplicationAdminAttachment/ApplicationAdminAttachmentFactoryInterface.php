<?php

namespace App\Model\Service\ApplicationAdminAttachment;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAdminAttachment;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Creates application admin attachment entities.
 */
interface ApplicationAdminAttachmentFactoryInterface
{
    /**
     * Creates an application admin attachment entity for the given file.
     *
     * @param File $file
     * @param Application $application
     * @param string $label
     * @return ApplicationAdminAttachment
     */
    public function createApplicationAdminAttachment(File $file, Application $application, string $label): ApplicationAdminAttachment;
}