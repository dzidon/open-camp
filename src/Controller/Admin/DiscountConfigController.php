<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\DiscountConfigData;
use App\Library\Data\Admin\DiscountConfigSearchData;
use App\Model\Entity\DiscountConfig;
use App\Model\Event\Admin\DiscountConfig\DiscountConfigCreateEvent;
use App\Model\Event\Admin\DiscountConfig\DiscountConfigDeleteEvent;
use App\Model\Event\Admin\DiscountConfig\DiscountConfigUpdateEvent;
use App\Model\Repository\DiscountConfigRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\DiscountConfigSearchType;
use App\Service\Form\Type\Admin\DiscountConfigType;
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
class DiscountConfigController extends AbstractController
{
    private DiscountConfigRepositoryInterface $discountConfigRepository;

    public function __construct(DiscountConfigRepositoryInterface $discountConfigRepository)
    {
        $this->discountConfigRepository = $discountConfigRepository;
    }

    #[IsGranted(new Expression('is_granted("discount_config", "any_admin_permission")'))]
    #[Route('/admin/discount-configs', name: 'admin_discount_config_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new DiscountConfigSearchData();
        $form = $formFactory->createNamed('', DiscountConfigSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new DiscountConfigSearchData();
        }

        $paginator = $this->discountConfigRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/discount_config/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('discount_config_create')]
    #[Route('/admin/discount-config/create', name: 'admin_discount_config_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $discountConfigData = new DiscountConfigData();

        $form = $this->createForm(DiscountConfigType::class, $discountConfigData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.discount_config.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new DiscountConfigCreateEvent($discountConfigData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.discount_config.create');

            return $this->redirectToRoute('admin_discount_config_list');
        }

        return $this->render('admin/discount_config/update.html.twig', [
            'form_discount_config' => $form->createView(),
            'breadcrumbs'          => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('discount_config_read')]
    #[Route('/admin/discount-config/{id}/read', name: 'admin_discount_config_read')]
    public function read(UuidV4 $id): Response
    {
        $discountConfig = $this->findDiscountConfigOrThrow404($id);

        return $this->render('admin/discount_config/read.html.twig', [
            'discount_config' => $discountConfig,
            'breadcrumbs'     => $this->createBreadcrumbs([
                'discount_config' => $discountConfig,
            ]),
        ]);
    }

    #[IsGranted('discount_config_update')]
    #[Route('/admin/discount-config/{id}/update', name: 'admin_discount_config_update')]
    public function update(EventDispatcherInterface $eventDispatcher, DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $discountConfig = $this->findDiscountConfigOrThrow404($id);

        $discountConfigData = new DiscountConfigData($discountConfig);
        $dataTransfer->fillData($discountConfigData, $discountConfig);

        $form = $this->createForm(DiscountConfigType::class, $discountConfigData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.discount_config.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new DiscountConfigUpdateEvent($discountConfigData, $discountConfig);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.discount_config.update');

            return $this->redirectToRoute('admin_discount_config_list');
        }

        return $this->render('admin/discount_config/update.html.twig', [
            'discount_config'      => $discountConfig,
            'form_discount_config' => $form->createView(),
            'breadcrumbs'          => $this->createBreadcrumbs([
                'discount_config' => $discountConfig,
            ]),
        ]);
    }

    #[IsGranted('discount_config_delete')]
    #[Route('/admin/discount-config/{id}/delete', name: 'admin_discount_config_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $discountConfig = $this->findDiscountConfigOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.discount_config_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new DiscountConfigDeleteEvent($discountConfig);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.discount_config.delete');

            return $this->redirectToRoute('admin_discount_config_list');
        }

        return $this->render('admin/discount_config/delete.html.twig', [
            'discount_config' => $discountConfig,
            'form_delete'     => $form->createView(),
            'breadcrumbs'     => $this->createBreadcrumbs([
                'discount_config' => $discountConfig,
            ]),
        ]);
    }

    private function findDiscountConfigOrThrow404(UuidV4 $id): DiscountConfig
    {
        $discountConfig = $this->discountConfigRepository->findOneById($id);

        if ($discountConfig === null)
        {
            throw $this->createNotFoundException();
        }

        return $discountConfig;
    }
}