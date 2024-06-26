<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationCamperSearchData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperCreateEvent;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperDeleteEvent;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperUpdateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Service\Data\Factory\ApplicationCamper\ApplicationCamperDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ApplicationCamperSearchType;
use App\Service\Form\Type\Common\ApplicationCamperType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationCamperController extends AbstractController
{
    private ApplicationCamperRepositoryInterface $applicationCamperRepository;

    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationCamperRepositoryInterface $applicationCamperRepository,
                                ApplicationRepositoryInterface       $applicationRepository)
    {
        $this->applicationCamperRepository = $applicationCamperRepository;
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/admin/application-campers/{campDateId}', name: 'admin_camp_date_application_camper_list')]
    public function listPerCampDate(CampDateRepositoryInterface      $campDateRepository,
                                    MenuTypeFactoryRegistryInterface $menuFactory,
                                    FormFactoryInterface             $formFactory,
                                    Request                          $request,
                                    ?UuidV4                          $campDateId = null): Response
    {
        $campDate = null;
        $searchByGuideOrCampDate = null;
        $isAdmin = $this->isGranted('application_read') || $this->isGranted('application_update');

        if ($campDateId === null)
        {
            $isGuide = $this->isGranted('guide_access_read');

            if (!$isGuide && !$isAdmin)
            {
                throw $this->createAccessDeniedException();
            }

            if ($isGuide && !$isAdmin)
            {
                /** @var User $searchByGuideOrCampDate */
                $searchByGuideOrCampDate = $this->getUser();
            }
        }
        else
        {
            $campDate = $campDateRepository->findOneById($campDateId);

            if ($campDate === null)
            {
                throw $this->createNotFoundException();
            }

            $isGuide = $this->isGranted('guide_access_read', $campDate);

            if (!$isAdmin && !$isGuide)
            {
                throw $this->createAccessDeniedException();
            }

            $searchByGuideOrCampDate = $campDate;
        }

        $page = (int) $request->query->get('page', 1);
        $searchData = new ApplicationCamperSearchData(true);
        $form = $formFactory->createNamed('', ApplicationCamperSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new ApplicationCamperSearchData(true);
        }

        $paginator = $this->applicationCamperRepository->getAdminPaginator($searchData, $searchByGuideOrCampDate, $page, 20);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        $camp = $campDate?->getCamp();

        return $this->render('admin/application/camper/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs([
                'camp_date' => $campDate,
                'camp'      => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application/{id}/campers', name: 'admin_application_camper_list')]
    public function listPerApplication(MenuTypeFactoryRegistryInterface $menuFactory,
                                       FormFactoryInterface             $formFactory,
                                       Request                          $request,
                                       UuidV4                           $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedUpdate($application);

        $page = (int) $request->query->get('page', 1);
        $searchData = new ApplicationCamperSearchData(false);
        $form = $formFactory->createNamed('', ApplicationCamperSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new ApplicationCamperSearchData(false);
        }

        $paginator = $this->applicationCamperRepository->getAdminPaginator($searchData, $application, $page, 20);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/camper/list.html.twig', [
            'application'       => $application,
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application/{id}/create-camper', name: 'admin_application_camper_create')]
    public function create(EventDispatcherInterface              $eventDispatcher,
                           ApplicationCamperDataFactoryInterface $applicationCamperDataFactory,
                           Request                               $request,
                           UuidV4                                $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedUpdate($application);

        $applicationCamperData = $applicationCamperDataFactory->createFromApplicationForAdminModule($application);
        $form = $this->createForm(ApplicationCamperType::class, $applicationCamperData);
        $form->add('submit', SubmitType::class, ['label' => 'form.common.application_camper.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationCamperCreateEvent($applicationCamperData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_camper.create');

            return $this->redirectToRoute('admin_application_camper_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/camper/update.html.twig', [
            'application'             => $application,
            'form_application_camper' => $form->createView(),
            'breadcrumbs'             => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-camper/{id}/read', name: 'admin_application_camper_read')]
    public function read(UuidV4 $id): Response
    {
        $applicationCamper = $this->findApplicationCamperOrThrow404($id);
        $application = $applicationCamper->getApplication();
        $this->assertIsGrantedRead($application);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/camper/read.html.twig', [
            'application'        => $application,
            'application_camper' => $applicationCamper,
            'breadcrumbs'        => $this->createBreadcrumbs([
                'is_parent_camp_date_list' => !$this->isGrantedUpdate($application),
                'application_camper'       => $applicationCamper,
                'application'              => $application,
                'camp_date'                => $campDate,
                'camp'                     => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-camper/{id}/update', name: 'admin_application_camper_update')]
    public function update(EventDispatcherInterface              $eventDispatcher,
                           DataTransferRegistryInterface         $dataTransfer,
                           ApplicationCamperDataFactoryInterface $applicationCamperDataFactory,
                           Request                               $request,
                           UuidV4                                $id): Response
    {
        $applicationCamper = $this->findApplicationCamperOrThrow404($id);
        $application = $applicationCamper->getApplication();
        $this->assertIsGrantedUpdate($application);

        $applicationCamperData = $applicationCamperDataFactory->createFromApplicationCamperForAdminModule($applicationCamper);
        $dataTransfer->fillData($applicationCamperData, $applicationCamper);

        $form = $this->createForm(ApplicationCamperType::class, $applicationCamperData);
        $form->add('submit', SubmitType::class, ['label' => 'form.common.application_camper.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationCamperUpdateEvent($applicationCamperData, $applicationCamper);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_camper.update');

            return $this->redirectToRoute('admin_application_camper_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/camper/update.html.twig', [
            'application'             => $application,
            'application_camper'      => $applicationCamper,
            'form_application_camper' => $form->createView(),
            'breadcrumbs'             => $this->createBreadcrumbs([
                'application_camper' => $applicationCamper,
                'application'        => $application,
                'camp_date'          => $campDate,
                'camp'               => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-camper/{id}/delete', name: 'admin_application_camper_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $applicationCamper = $this->findApplicationCamperOrThrow404($id);
        $application = $applicationCamper->getApplication();
        $this->assertIsGrantedUpdate($application);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.application_camper_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationCamperDeleteEvent($applicationCamper);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_camper.delete');

            return $this->redirectToRoute('admin_application_camper_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/camper/delete.html.twig', [
            'application'        => $application,
            'application_camper' => $applicationCamper,
            'form_delete'        => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs([
                'application_camper' => $applicationCamper,
                'application'        => $application,
                'camp_date'          => $campDate,
                'camp'               => $camp,
            ]),
        ]);
    }

    private function isGrantedUpdate(Application|CampDate $subject): bool
    {
        return $this->isGranted('application_update') || $this->isGranted('guide_access_update', $subject);
    }

    private function assertIsGrantedUpdate(Application|CampDate $subject): void
    {
        if (!$this->isGrantedUpdate($subject))
        {
            throw $this->createAccessDeniedException();
        }
    }

    private function assertIsGrantedRead(Application|CampDate $subject): void
    {
        if (!$this->isGranted('application_read') && !$this->isGranted('guide_access_read', $subject))
        {
            throw $this->createAccessDeniedException();
        }
    }

    private function findApplicationCamperOrThrow404(UuidV4 $id): ApplicationCamper
    {
        $applicationCamper = $this->applicationCamperRepository->findOneById($id);

        if ($applicationCamper === null)
        {
            throw $this->createNotFoundException();
        }

        return $applicationCamper;
    }

    private function findApplicationOrThrow404(UuidV4 $id): Application
    {
        $application = $this->applicationRepository->findOneById($id);

        if ($application === null)
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}