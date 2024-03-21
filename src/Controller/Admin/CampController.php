<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\CampCreationData;
use App\Library\Data\Admin\CampData;
use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Data\Admin\CampSearchData;
use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use App\Model\Entity\Camp;
use App\Model\Event\Admin\Camp\CampCreateEvent;
use App\Model\Event\Admin\Camp\CampDeleteEvent;
use App\Model\Event\Admin\Camp\CampUpdateEvent;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\CampCreationType;
use App\Service\Form\Type\Admin\CampSearchType;
use App\Service\Form\Type\Admin\CampType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class CampController extends AbstractController
{
    private CampRepositoryInterface $campRepository;

    public function __construct(CampRepositoryInterface $campRepository)
    {
        $this->campRepository = $campRepository;
    }

    #[IsGranted(new Expression('is_granted("camp", "any_admin_permission")'))]
    #[Route('/admin/camps', name: 'admin_camp_list')]
    public function list(FormFactoryInterface             $formFactory,
                         MenuTypeFactoryRegistryInterface $menuFactory,
                         CampCategoryRepositoryInterface  $campCategoryRepository,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $campCategoryChoices = $campCategoryRepository->findAll();

        $searchData = new CampSearchData();
        $form = $formFactory->createNamed('', CampSearchType::class, $searchData, ['choices_camp_categories' => $campCategoryChoices]);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new CampSearchData();
        }

        $paginator = $this->campRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);
        $campLifespans = $this->campRepository->getCampLifespanCollection($paginator->getCurrentPageItems());

        return $this->render('admin/camp/list.html.twig', [
            'form_search'       => $form->createView(),
            'camp_lifespans'    => $campLifespans,
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('camp_create')]
    #[Route('/admin/camp/create', name: 'admin_camp_create')]
    public function create(EventDispatcherInterface        $eventDispatcher,
                           CampCategoryRepositoryInterface $campCategoryRepository,
                           Request                         $request): Response
    {
        $campCategoryChoices = $campCategoryRepository->findAll();

        $campCreationData = new CampCreationData();
        $form = $this->createForm(CampCreationType::class, $campCreationData, [
            'choices_camp_categories' => $campCategoryChoices
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_creation.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new CampCreateEvent($campCreationData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.camp.create');

            return $this->redirectToRoute('admin_camp_list');
        }

        return $this->render('admin/camp/update.html.twig', [
            'form_camp'   => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('camp_read')]
    #[Route('/admin/camp/{id}/read', name: 'admin_camp_read')]
    public function read(CampCategoryRepositoryInterface $campCategoryRepository,
                         CampImageRepositoryInterface    $campImageRepository,
                         CampDateRepositoryInterface     $campDateRepository,
                         UuidV4                          $id): Response
    {
        $camp = $this->findCampOrThrow404($id);

        // load all camp categories so that the camp category path does not trigger additional queries
        $campCategoryRepository->findAll();

        // images
        $campImages = $campImageRepository->findByCamp($camp);

        // camp dates
        $searchData = new CampDateSearchData();
        $searchData->setIsHistorical(null);
        $searchData->setSortBy(CampDateSortEnum::START_AT_DESC);
        $campDatePaginator = $campDateRepository->getAdminPaginator($searchData, $camp, 1, 20);
        $campDates = $campDatePaginator->getCurrentPageItems();
        $moreCampDates = max($campDatePaginator->getTotalItems() - $campDatePaginator->getPageSize(), 0);

        return $this->render('admin/camp/read.html.twig', [
            'camp'            => $camp,
            'camp_images'     => $campImages,
            'camp_dates'      => $campDates,
            'more_camp_dates' => $moreCampDates,
            'breadcrumbs'     => $this->createBreadcrumbs([
                'camp' => $camp,
            ]),
        ]);
    }

    #[IsGranted('camp_update')]
    #[Route('/admin/camp/{id}/update', name: 'admin_camp_update')]
    public function update(EventDispatcherInterface        $eventDispatcher,
                           DataTransferRegistryInterface   $dataTransfer,
                           CampCategoryRepositoryInterface $campCategoryRepository,
                           Request                         $request,
                           UuidV4                          $id): Response
    {
        $camp = $this->findCampOrThrow404($id);
        $campCategoryChoices = $campCategoryRepository->findAll();

        $campData = new CampData($camp);
        $dataTransfer->fillData($campData, $camp);

        $form = $this->createForm(CampType::class, $campData, ['choices_camp_categories' => $campCategoryChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new CampUpdateEvent($campData, $camp);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.camp.update');

            return $this->redirectToRoute('admin_camp_list');
        }

        return $this->render('admin/camp/update.html.twig', [
            'camp'        => $camp,
            'form_camp'   => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'camp' => $camp,
            ]),
        ]);
    }

    #[IsGranted('camp_delete')]
    #[Route('/admin/camp/{id}/delete', name: 'admin_camp_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $camp = $this->findCampOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.camp_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new CampDeleteEvent($camp);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.camp.delete');

            return $this->redirectToRoute('admin_camp_list');
        }

        return $this->render('admin/camp/delete.html.twig', [
            'camp'        => $camp,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'camp' => $camp,
            ]),
        ]);
    }

    private function findCampOrThrow404(UuidV4 $id): Camp
    {
        $camp = $this->campRepository->findOneById($id);
        if ($camp === null)
        {
            throw $this->createNotFoundException();
        }

        return $camp;
    }
}