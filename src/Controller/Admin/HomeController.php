<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/admin')]
class HomeController extends AbstractController
{
    #[IsGranted('_any_permission')]
    #[Route('', name: 'admin_home')]
    public function index(): Response
    {
        return $this->render('admin/home/index.html.twig');
    }
}