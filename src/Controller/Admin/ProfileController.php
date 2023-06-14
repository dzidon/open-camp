<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbsInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/profile')]
class ProfileController extends AbstractController
{
    private ProfileBreadcrumbsInterface $breadcrumbs;

    public function __construct(ProfileBreadcrumbsInterface $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('', name: 'admin_profile')]
    public function profile(): Response
    {
        return $this->render('admin/profile/profile.html.twig', [
            '_breadcrumbs' => $this->breadcrumbs->buildProfile()
        ]);
    }
}