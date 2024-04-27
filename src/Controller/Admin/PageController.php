<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PageData;
use App\Library\Data\Admin\PageSearchData;
use App\Model\Entity\Page;
use App\Model\Event\Admin\Page\PageCreateEvent;
use App\Model\Event\Admin\Page\PageDeleteEvent;
use App\Model\Event\Admin\Page\PageUpdateEvent;
use App\Model\Repository\PageRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\PageSearchType;
use App\Service\Form\Type\Admin\PageType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class PageController extends AbstractController
{
    private PageRepositoryInterface $pageRepository;

    public function __construct(PageRepositoryInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    #[IsGranted(new Expression('is_granted("page", "any_admin_permission")'))]
    #[Route('/admin/pages', name: 'admin_page_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new PageSearchData();
        $form = $formFactory->createNamed('', PageSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new PageSearchData();
        }

        $paginator = $this->pageRepository->getAdminPaginator($searchData, $page, 20);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/page/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('page_create')]
    #[Route('/admin/page/create', name: 'admin_page_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $pageData = new PageData();
        $form = $this->createForm(PageType::class, $pageData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.page.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PageCreateEvent($pageData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.page.create');

            return $this->redirectToRoute('admin_page_list');
        }

        return $this->render('admin/page/update.html.twig', [
            'form_page'   => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('page_read')]
    #[Route('/admin/page/{id}/read', name: 'admin_page_read')]
    public function read(UuidV4 $id): Response
    {
        $page = $this->findPageOrThrow404($id);

        return $this->render('admin/page/read.html.twig', [
            'page'        => $page,
            'breadcrumbs' => $this->createBreadcrumbs([
                'page' => $page,
            ]),
        ]);
    }

    #[IsGranted('page_update')]
    #[Route('/admin/page/{id}/update', name: 'admin_page_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $page = $this->findPageOrThrow404($id);
        $pageData = new PageData($page);
        $dataTransfer->fillData($pageData, $page);

        $form = $this->createForm(PageType::class, $pageData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.page.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PageUpdateEvent($pageData, $page);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.page.update');

            return $this->redirectToRoute('admin_page_list');
        }

        return $this->render('admin/page/update.html.twig', [
            'page'        => $page,
            'form_page'   => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'page' => $page,
            ]),
        ]);
    }

    #[IsGranted('page_delete')]
    #[Route('/admin/page/{id}/delete', name: 'admin_page_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $page = $this->findPageOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.page_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PageDeleteEvent($page);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.page.delete');

            return $this->redirectToRoute('admin_page_list');
        }

        return $this->render('admin/page/delete.html.twig', [
            'page'        => $page,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'page' => $page,
            ]),
        ]);
    }

    private function findPageOrThrow404(UuidV4 $id): Page
    {
        $page = $this->pageRepository->findOneById($id);

        if ($page === null)
        {
            throw $this->createNotFoundException();
        }

        return $page;
    }
}