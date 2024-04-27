<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Entity\Page;
use App\Model\Repository\PageRepositoryInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private PageRepositoryInterface $pageRepository;

    public function __construct(PageRepositoryInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    #[Route('/{urlName}', name: 'user_page_read', requirements: ['path' => '.+'], priority: -1000)]
    public function read(RouteNamerInterface $routeNamer, string $urlName): Response
    {
        $page = $this->findPageOrThrow404($urlName);

        if ($page->isHidden())
        {
            if (!$this->userCanViewHiddenPages())
            {
                throw $this->createNotFoundException();
            }

            $this->addTransFlash('warning', 'custom_content.hidden_page_shown_for_admin');
        }

        $title = $page->getTitle();
        $routeNamer->setCurrentRouteName($title);

        return $this->render('user/page/read.html.twig', [
            'page'        => $page,
            'breadcrumbs' => $this->createBreadcrumbs([
                'page' => $page,
            ]),
        ]);
    }

    private function userCanViewHiddenPages(): bool
    {
        return
            $this->isGranted('page_read')   ||
            $this->isGranted('page_create') ||
            $this->isGranted('page_update')
        ;
    }

    private function findPageOrThrow404(string $urlName): Page
    {
        $page = $this->pageRepository->findOneByUrlName($urlName);

        if ($page === null)
        {
            throw $this->createNotFoundException();
        }

        return $page;
    }
}