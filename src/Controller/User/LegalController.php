<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/privacy', name: 'user_privacy')]
    public function privacy(): Response
    {
        return $this->render('user/legal/privacy.html.twig', [
            'breadcrumbs' => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/terms-of-use', name: 'user_terms_of_use')]
    public function termsOfUse(): Response
    {
        return $this->render('user/legal/terms_of_use.html.twig', [
            'breadcrumbs' => $this->createBreadcrumbs(),
        ]);
    }
}