<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'user_home')]
    public function index(RouteNamerInterface $routeNamer): Response
    {
        $routeNamer->setCurrentRouteName(null);

        return $this->render('user/home/index.html.twig');
    }
}