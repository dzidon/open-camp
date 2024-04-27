<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuideController extends AbstractController
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/guides', name: 'user_guide_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory, Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $paginator = $this->userRepository->getUserGuidePaginator(false, $page, 24);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('user/guide/list.html.twig', [
            'pagination_menu' => $paginationMenu,
            'paginator'       => $paginator,
            'breadcrumbs'     => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/guide/{urlName}', name: 'user_guide_detail', requirements: ['urlName' => '([a-zA-Z0-9-])+'])]
    public function detail(RouteNamerInterface $routeNamer, string $urlName): Response
    {
        $guide = $this->findGuideOrThrow404($urlName);
        $guideNameFull = $guide->getNameFull();

        if ($guideNameFull === null)
        {
            $unnamedGuideText = $this->trans('guide.unnamed');
            $routeNamer->setCurrentRouteName($unnamedGuideText);
        }
        else
        {
            $routeNamer->appendToCurrentRouteName($guideNameFull);
        }

        return $this->render('user/guide/detail.html.twig', [
            'guide'       => $guide,
            'breadcrumbs' => $this->createBreadcrumbs([
                'guide' => $guide,
            ]),
        ]);
    }

    private function findGuideOrThrow404(string $urlName): User
    {
        $user = $this->userRepository->findOneByUrlName($urlName);

        if ($user === null)
        {
            throw $this->createNotFoundException();
        }

        return $user;
    }
}