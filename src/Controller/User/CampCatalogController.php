<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\CampSearchData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Model\Service\Application\ApplicationDraftHttpStorageInterface;
use App\Service\Form\Type\User\CampSearchType;
use App\Service\Menu\Breadcrumbs\User\CampCatalogBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampCatalogController extends AbstractController
{
    private CampRepositoryInterface $campRepository;
    private CampCategoryRepositoryInterface $campCategoryRepository;
    private CampCatalogBreadcrumbsInterface $breadcrumbs;
    private RouteNamerInterface $routeNamer;

    public function __construct(CampRepositoryInterface         $campRepository,
                                CampCategoryRepositoryInterface $campCategoryRepository,
                                CampCatalogBreadcrumbsInterface $breadcrumbs,
                                RouteNamerInterface             $routeNamer)
    {
        $this->campRepository = $campRepository;
        $this->campCategoryRepository = $campCategoryRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->routeNamer = $routeNamer;
    }

    #[Route('/catalog/{path}', name: 'user_camp_catalog', requirements: ['path' => '.+'])]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request,
                         ?string                          $path = null): Response
    {
        $campCategory = null;
        $pathCampCategories = [];
        $showHiddenCamps = $this->userCanViewHiddenCamps();

        if ($path === null || $path === '')
        {
            $campCategoryChildren = $this->campCategoryRepository->findRoots();
        }
        else
        {
            $campCategory = $this->findCampCategoryOrThrow404($path, $showHiddenCamps);
            $this->routeNamer->setCurrentRouteName($campCategory->getName());
            $campCategoryChildren = $campCategory->getChildren();
            $pathCampCategories = array_merge($campCategory->getAncestors(), [$campCategory]); // for breadcrumbs
        }

        $searchData = new CampSearchData();
        $form = $formFactory->createNamed('', CampSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new CampSearchData();
        }

        $campCategoryChildren = $this->campCategoryRepository->filterOutCampCategoriesWithoutCamps($campCategoryChildren, $showHiddenCamps);

        $page = (int) $request->query->get('page', 1);
        $result = $this->campRepository->getUserCampCatalogResult($searchData, $campCategory, $showHiddenCamps, $page, 12);
        $paginator = $result->getPaginator();
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        return $this->render('user/camp_catalog/list.html.twig', [
            'form_search'            => $form->createView(),
            'camp_category'          => $campCategory,
            'camp_category_children' => $campCategoryChildren,
            'catalog_result'         => $result,
            'pagination_menu'        => $paginationMenu,
            'is_search_invalid'      => $isSearchInvalid,
            'breadcrumbs'            => $this->breadcrumbs->buildList($pathCampCategories),
        ]);
    }

    #[Route('/camp/{urlName}', name: 'user_camp_detail', requirements: ['urlName' => '([a-zA-Z0-9-])+'])]
    public function detail(CampDateRepositoryInterface          $campDateRepository,
                           CampImageRepositoryInterface         $campImageRepository,
                           UserRepositoryInterface              $userRepository,
                           ApplicationRepositoryInterface       $applicationRepository,
                           ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage,
                           string                               $urlName): Response
    {
        $camp = $this->findCampOrThrow404($urlName);
        $showHiddenCamps = $this->userCanViewHiddenCamps();

        if ($camp->isHidden())
        {
            if (!$showHiddenCamps)
            {
                throw $this->createNotFoundException();
            }

            $this->addTransFlash('warning', 'camp_catalog.hidden_camp_detail_shown_for_admin');
        }

        $campDatesResult = $campDateRepository->findUpcomingByCamp($camp, $showHiddenCamps);
        $campDates = $campDatesResult->getCampDates();
        $guides = $userRepository->findByCampDates($campDates, true);
        $campImages = $campImageRepository->findByCamp($camp);
        $storedApplicationDraftIds = $applicationDraftHttpStorage->getApplicationDraftIds();
        $applicationsEditableDraftsResult = $applicationRepository->getApplicationsEditableDraftsResult($storedApplicationDraftIds);

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();

        $campName = $camp->getName();
        $this->routeNamer->setCurrentRouteName($campName);

        return $this->render('user/camp_catalog/detail.html.twig', [
            'camp'                                => $camp,
            'guides'                              => $guides,
            'camp_images'                         => $campImages,
            'camp_dates_result'                   => $campDatesResult,
            'applications_editable_drafts_result' => $applicationsEditableDraftsResult,
            'breadcrumbs'                         => $this->breadcrumbs->buildDetail($camp),
        ]);
    }

    private function userCanViewHiddenCamps(): bool
    {
        return $this->isGranted('camp_create') || $this->isGranted('camp_update');
    }

    private function findCampCategoryOrThrow404(string $path, bool $showHiddenCamps): CampCategory
    {
        $campCategory = $this->campCategoryRepository->findOneByPath($path);

        if ($campCategory === null || !$this->campCategoryRepository->campCategoryHasCamp($campCategory, $showHiddenCamps))
        {
            throw $this->createNotFoundException();
        }

        return $campCategory;
    }

    private function findCampOrThrow404(string $urlName): Camp
    {
        $camp = $this->campRepository->findOneByUrlName($urlName);

        if ($camp === null)
        {
            throw $this->createNotFoundException();
        }

        return $camp;
    }
}