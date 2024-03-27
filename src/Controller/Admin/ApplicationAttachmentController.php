<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Http\Response\FileDownloadResponse;
use App\Model\Repository\ApplicationAttachmentRepositoryInterface;
use App\Model\Service\ApplicationAttachment\ApplicationAttachmentFilesystemInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationAttachmentController extends AbstractController
{
    #[Route('/admin/application-attachment/{id}', name: 'admin_application_attachment')]
    public function attachment(ApplicationAttachmentRepositoryInterface $applicationAttachmentRepository,
                               ApplicationAttachmentFilesystemInterface $applicationAttachmentFilesystem,
                               UuidV4                                   $id): FileDownloadResponse
    {
        $applicationAttachment = $applicationAttachmentRepository->findOneById($id);

        if ($applicationAttachment === null)
        {
            throw $this->createNotFoundException();
        }

        $application = $applicationAttachment->getApplication();

        if ($application === null)
        {
            $application = $applicationAttachment
                ->getApplicationCamper()
                ->getApplication()
            ;
        }

        if (!$this->isGranted('application_read') && !$this->isGranted('application_guide_read', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $fileContents = $applicationAttachmentFilesystem->getFileContents($applicationAttachment);

        if ($fileContents === null)
        {
            throw $this->createNotFoundException();
        }

        $fileName = $applicationAttachment->getLabel();
        $fileExtension = $applicationAttachment->getExtension();

        return new FileDownloadResponse($fileName, $fileExtension, $fileContents);
    }
}