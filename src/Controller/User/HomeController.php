<?php

namespace App\Controller\User;

use App\Translation\LocaleRedirectResponseFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function noLocale(LocaleRedirectResponseFactoryInterface $responseFactory, Request $request): Response
    {
        return $responseFactory->createRedirectResponse($request, 'user_home');
    }

    #[Route('/', name: 'user_home')]
    public function index(): Response
    {
        return $this->render('user/home/index.html.twig');
    }
}