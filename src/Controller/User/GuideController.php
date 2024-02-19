<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Menu\Breadcrumbs\User\GuideBreadcrumbsInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuideController extends AbstractController
{
    private UserRepositoryInterface $userRepository;

    private GuideBreadcrumbsInterface $breadcrumbs;

    public function __construct(UserRepositoryInterface $userRepository, GuideBreadcrumbsInterface $breadcrumbs)
    {
        $this->userRepository = $userRepository;
        $this->breadcrumbs = $breadcrumbs;
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
            'guide'        => $guide,
            'breadcrumbs'  => $this->breadcrumbs->buildDetail($guide),
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