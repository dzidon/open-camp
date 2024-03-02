<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Http\Response\PdfResponse;
use App\Model\Entity\Application;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationInvoiceFilesystemInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/profile')]
class ProfileApplicationController extends AbstractController
{
    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationRepositoryInterface $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/application/{id}/invoice', name: 'user_profile_application_invoice')]
    public function invoice(ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem, UuidV4 $id): PdfResponse
    {
        $application = $this->findCompletedApplicationOrThrow404($id);
        $this->denyAccessUnlessGranted('application_read', $application);

        $invoiceContents = $applicationInvoiceFilesystem->getInvoiceContents($application);

        if ($invoiceContents === null)
        {
            throw $this->createNotFoundException();
        }

        return new PdfResponse('', $invoiceContents);
    }

    private function findCompletedApplicationOrThrow404(UuidV4 $id): Application
    {
        $application = $this->applicationRepository->findOneById($id);

        if ($application === null || $application->isDraft())
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}