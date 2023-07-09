<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Menu\Breadcrumbs\Admin\ProfileBreadcrumbsInterface;
use App\Model\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/admin/profile')]
class ProfileController extends AbstractController
{
    private ProfileBreadcrumbsInterface $breadcrumbs;

    public function __construct(ProfileBreadcrumbsInterface $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    #[IsGranted('_any_permission')]
    #[Route('', name: 'admin_profile')]
    public function profile(): Response
    {
        /** @var User $admin */
        $admin = $this->getUser();

        return $this->render('admin/profile/profile.html.twig', [
            'admin'        => $admin,
            '_breadcrumbs' => $this->breadcrumbs->buildProfile(),
        ]);
    }
}