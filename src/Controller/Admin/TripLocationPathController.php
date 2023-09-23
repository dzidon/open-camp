<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Library\Data\Admin\TripLocationPathData;
use App\Library\Data\Admin\TripLocationPathSearchData;
use App\Library\Data\Admin\TripLocationSearchData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Model\Repository\TripLocationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\TripLocationPathCreationType;
use App\Service\Form\Type\Admin\TripLocationPathSearchType;
use App\Service\Form\Type\Admin\TripLocationPathType;
use App\Service\Form\Type\Admin\TripLocationSearchType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\TripLocationPathBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class TripLocationPathController extends AbstractController
{
    private TripLocationPathRepositoryInterface $tripLocationPathRepository;
    private TripLocationPathBreadcrumbsInterface $breadcrumbs;

    public function __construct(TripLocationPathRepositoryInterface  $tripLocationPathRepository,
                                TripLocationPathBreadcrumbsInterface $breadcrumbs)
    {
        $this->tripLocationPathRepository = $tripLocationPathRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("trip_location_path_create") or is_granted("trip_location_path_read") or 
                                         is_granted("trip_location_path_update") or is_granted("trip_location_path_delete")'))]
    #[Route('/admin/trip-location-paths', name: 'admin_trip_location_path_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new TripLocationPathSearchData();
        $form = $formFactory->createNamed('', TripLocationPathSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new TripLocationPathSearchData();
        }

        $paginator = $this->tripLocationPathRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/trip/location_path/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->breadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('trip_location_path_create')]
    #[Route('/admin/trip-location-path/create', name: 'admin_trip_location_path_create')]
    public function create(DataTransferRegistryInterface   $dataTransfer,
                           TripLocationRepositoryInterface $tripLocationRepository,
                           Request                         $request): Response
    {
        $pathCreationData = new TripLocationPathCreationData();

        $form = $this->createForm(TripLocationPathCreationType::class, $pathCreationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.trip_location_path_creation.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // path
            $tripLocationPathData = $pathCreationData->getTripLocationPathData();
            $tripLocationPath = new TripLocationPath($tripLocationPathData->getName());
            $dataTransfer->fillEntity($tripLocationPathData, $tripLocationPath);

            // locations
            $tripLocationsData = $pathCreationData->getTripLocationsData();
            foreach ($tripLocationsData as $tripLocationData)
            {
                $tripLocation = new TripLocation($tripLocationData->getName(), $tripLocationData->getPrice(), $tripLocationData->getPriority(), $tripLocationPath);
                $dataTransfer->fillEntity($tripLocationData, $tripLocation);
                $tripLocationRepository->saveTripLocation($tripLocation, false);
            }

            $this->tripLocationPathRepository->saveTripLocationPath($tripLocationPath, true);
            $this->addTransFlash('success', 'crud.action_performed.trip_location_path.create');

            return $this->redirectToRoute('admin_trip_location_path_list');
        }

        return $this->render('admin/trip/location_path/update.html.twig', [
            'form_trip_location_path' => $form->createView(),
            'breadcrumbs'             => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('trip_location_path_read')]
    #[Route('/admin/trip-location-path/{id}/read', name: 'admin_trip_location_path_read')]
    public function read(TripLocationRepositoryInterface $tripLocationRepository, UuidV4 $id): Response
    {
        $tripLocationPath = $this->findTripLocationPathOrThrow404($id);
        $tripLocations = $tripLocationRepository->findByTripLocationPath($tripLocationPath);

        return $this->render('admin/trip/location_path/read.html.twig', [
            'trip_location_path' => $tripLocationPath,
            'trip_locations'     => $tripLocations,
            'breadcrumbs'        => $this->breadcrumbs->buildRead($tripLocationPath),
        ]);
    }

    #[IsGranted('trip_location_path_update')]
    #[Route('/admin/trip-location-path/{id}/update', name: 'admin_trip_location_path_update')]
    public function update(TripLocationRepositoryInterface  $tripLocationRepository,
                           MenuTypeFactoryRegistryInterface $menuFactory,
                           FormFactoryInterface             $formFactory,
                           DataTransferRegistryInterface    $dataTransfer,
                           Request                          $request,
                           UuidV4                           $id): Response
    {
        $tripLocationPath = $this->findTripLocationPathOrThrow404($id);

        $updatePartPathResult = $this->updatePartPath($dataTransfer, $tripLocationPath, $request);
        if ($updatePartPathResult instanceof RedirectResponse)
        {
            return $updatePartPathResult;
        }

        $updatePartLocations = $this->updatePartLocations($tripLocationRepository, $menuFactory, $formFactory, $tripLocationPath, $request);
        if ($updatePartLocations instanceof RedirectResponse)
        {
            return $updatePartLocations;
        }

        return $this->render('admin/trip/location_path/update.html.twig', array_merge($updatePartPathResult, $updatePartLocations, [
            'breadcrumbs' => $this->breadcrumbs->buildUpdate($tripLocationPath),
        ]));
    }

    private function updatePartPath(DataTransferRegistryInterface $dataTransfer,
                                    TripLocationPath              $tripLocationPath,
                                    Request                       $request): array|RedirectResponse
    {
        $tripLocationPathData = new TripLocationPathData();
        $dataTransfer->fillData($tripLocationPathData, $tripLocationPath);

        $form = $this->createForm(TripLocationPathType::class, $tripLocationPathData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.trip_location_path.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($tripLocationPathData, $tripLocationPath);
            $this->tripLocationPathRepository->saveTripLocationPath($tripLocationPath, true);
            $this->addTransFlash('success', 'crud.action_performed.trip_location_path.update');

            return $this->redirectToRoute('admin_trip_location_path_list');
        }

        return [
            'trip_location_path'      => $tripLocationPath,
            'form_trip_location_path' => $form->createView(),
        ];
    }

    private function updatePartLocations(TripLocationRepositoryInterface  $tripLocationRepository,
                                         MenuTypeFactoryRegistryInterface $menuFactory,
                                         FormFactoryInterface             $formFactory,
                                         TripLocationPath                 $tripLocationPath,
                                         Request                          $request): array|RedirectResponse
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new TripLocationSearchData();
        $form = $formFactory->createNamed('', TripLocationSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new TripLocationSearchData();
        }

        $paginator = $tripLocationRepository->getAdminPaginator($searchData, $tripLocationPath, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
        ];
    }

    #[IsGranted('trip_location_path_delete')]
    #[Route('/admin/trip-location-path/{id}/delete', name: 'admin_trip_location_path_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $tripLocationPath = $this->findTripLocationPathOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.trip_location_path_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->tripLocationPathRepository->removeTripLocationPath($tripLocationPath, true);
            $this->addTransFlash('success', 'crud.action_performed.trip_location_path.delete');

            return $this->redirectToRoute('admin_trip_location_path_list');
        }

        return $this->render('admin/trip/location_path/delete.html.twig', [
            'trip_location_path' => $tripLocationPath,
            'form_delete'        => $form->createView(),
            'breadcrumbs'        => $this->breadcrumbs->buildDelete($tripLocationPath),
        ]);
    }

    private function findTripLocationPathOrThrow404(UuidV4 $id): TripLocationPath
    {
        $tripLocationPath = $this->tripLocationPathRepository->findOneById($id);
        if ($tripLocationPath === null)
        {
            throw $this->createNotFoundException();
        }

        return $tripLocationPath;
    }
}