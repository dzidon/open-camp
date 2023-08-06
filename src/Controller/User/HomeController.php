<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Service\Routing\RouteNamerInterface;
use App\Service\Translation\LocaleRedirectResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function noLocale(LocaleRedirectResponseFactoryInterface $responseFactory, Request $request): Response
    {
        return $responseFactory->createRedirectResponse($request, 'user_home');
    }

    #[Route('', name: 'user_home')]
    public function index(RouteNamerInterface $routeNamer): Response
    {
        $routeNamer->setCurrentRouteName(null);

        return $this->render('user/home/index.html.twig');
    }
}