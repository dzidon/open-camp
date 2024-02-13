<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationToUserImportEvent;
use App\Model\Event\User\Application\ApplicationToUserImportSkipEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationImportToUserDataFactoryInterface;
use App\Model\Service\Application\ApplicationToUserImporterInterface;
use App\Service\Form\Type\User\ApplicationImportToUserType;
use App\Service\Menu\Breadcrumbs\User\ApplicationToUserImportBreadcrumbsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationToUserImportController extends AbstractController
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationToUserImporterInterface $applicationToUserImporter;

    private EventDispatcherInterface $eventDispatcher;

    private ApplicationToUserImportBreadcrumbsInterface $breadcrumbs;

    public function __construct(ApplicationRepositoryInterface              $applicationRepository,
                                ApplicationToUserImporterInterface          $applicationToUserImporter,
                                EventDispatcherInterface                    $eventDispatcher,
                                ApplicationToUserImportBreadcrumbsInterface $breadcrumbs)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationToUserImporter = $applicationToUserImporter;
        $this->eventDispatcher = $eventDispatcher;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/application-to-user-import', name: 'user_application_import')]
    public function import(ApplicationImportToUserDataFactoryInterface $applicationImportToUserDataFactory,
                           Request                                     $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $application = $this->findApplicationFromSessionOrThrow404();

        if (!$this->applicationToUserImporter->canImportApplicationToUser($application, $user))
        {
            throw $this->createNotFoundException();
        }

        $data = $applicationImportToUserDataFactory->createApplicationImportToUserData($application, $user);
        $form = $this->createForm(ApplicationImportToUserType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_import_to_user.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationToUserImportEvent($data);
            $this->eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.user.import_application');

            return $this->redirectToRoute('user_application_completed', [
                'applicationId' => $application->getId(),
            ]);
        }

        return $this->render('user/import_application/import.html.twig', [
            'form_import' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildForApplicationImport($application),
        ]);
    }

    #[Route('/application-to-user-import-skip', name: 'user_application_import_skip')]
    public function importSkip(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $application = $this->findApplicationFromSessionOrThrow404();

        if (!$this->applicationToUserImporter->canImportApplicationToUser($application, $user))
        {
            throw $this->createNotFoundException();
        }

        $event = new ApplicationToUserImportSkipEvent();
        $this->eventDispatcher->dispatch($event, $event::NAME);
        $this->addTransFlash('success', 'crud.action_performed.user.import_application_skip');

        return $this->redirectToRoute('user_application_completed', [
            'applicationId' => $application->getId(),
        ]);
    }

    private function findApplicationFromSessionOrThrow404(): Application
    {
        $application = $this->applicationRepository->findLastCompletedFromSession();

        if ($application === null)
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}