<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\CampSearchData;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
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

        if ($path === null || $path === '')
        {
            $campCategoryChildren = $this->campCategoryRepository->findRoots();
        }
        else
        {
            $campCategory = $this->campCategoryRepository->findOneByPath($path);

            if ($campCategory === null)
            {
                throw $this->createNotFoundException();
            }

            $this->routeNamer->setCurrentRouteName($campCategory->getName());
            $campCategoryChildren = $campCategory->getChildren();
            $pathCampCategories = array_merge($campCategory->getAncestors(), [$campCategory]);
        }

        $searchData = new CampSearchData();
        $form = $formFactory->createNamed('', CampSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new CampSearchData();
        }

        $page = (int) $request->query->get('page', 1);
        $result = $this->campRepository->getUserCampCatalogResult($searchData, $campCategory, $page, 12);
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
}