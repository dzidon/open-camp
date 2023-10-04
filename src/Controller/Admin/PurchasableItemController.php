<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PurchasableItemData;
use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Data\Admin\PurchasableItemVariantSearchData;
use App\Model\Entity\PurchasableItem;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\PurchasableItemSearchType;
use App\Service\Form\Type\Admin\PurchasableItemType;
use App\Service\Form\Type\Admin\PurchasableItemVariantSearchType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\PurchasableItemBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class PurchasableItemController extends AbstractController
{
    private PurchasableItemRepositoryInterface $purchasableItemRepository;
    private PurchasableItemBreadcrumbsInterface $breadcrumbs;

    public function __construct(PurchasableItemRepositoryInterface  $purchasableItemRepository,
                                PurchasableItemBreadcrumbsInterface $breadcrumbs)
    {
        $this->purchasableItemRepository = $purchasableItemRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("purchasable_item_create") or is_granted("purchasable_item_read") or 
                                         is_granted("purchasable_item_update") or is_granted("purchasable_item_delete")'))]
    #[Route('/admin/purchasable-items', name: 'admin_purchasable_item_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new PurchasableItemSearchData();
        $form = $formFactory->createNamed('', PurchasableItemSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new PurchasableItemSearchData();
        }

        $paginator = $this->purchasableItemRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/purchasable_item/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->breadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('purchasable_item_create')]
    #[Route('/admin/purchasable-item/create', name: 'admin_purchasable_item_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        $purchasableItemData = new PurchasableItemData();

        $form = $this->createForm(PurchasableItemType::class, $purchasableItemData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $purchasableItem = new PurchasableItem($purchasableItemData->getName(), $purchasableItemData->getPrice(), $purchasableItemData->getMaxAmountPerCamper());
            $dataTransfer->fillEntity($purchasableItemData, $purchasableItem);
            $this->purchasableItemRepository->savePurchasableItem($purchasableItem, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item.create');

            return $this->redirectToRoute('admin_purchasable_item_list');
        }

        return $this->render('admin/purchasable_item/update.html.twig', [
            'form_purchasable_item' => $form->createView(),
            'breadcrumbs'           => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('purchasable_item_read')]
    #[Route('/admin/purchasable-item/{id}/read', name: 'admin_purchasable_item_read')]
    public function read(PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository, UuidV4 $id): Response
    {
        $purchasableItem = $this->findPurchasableItemOrThrow404($id);
        $purchasableItemVariants = $purchasableItemVariantRepository->findByPurchasableItem($purchasableItem);

        return $this->render('admin/purchasable_item/read.html.twig', [
            'purchasable_item'          => $purchasableItem,
            'purchasable_item_variants' => $purchasableItemVariants,
            'breadcrumbs'               => $this->breadcrumbs->buildRead($purchasableItem),
        ]);
    }

    #[IsGranted('purchasable_item_update')]
    #[Route('/admin/purchasable-item/{id}/update', name: 'admin_purchasable_item_update')]
    public function update(PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository,
                           MenuTypeFactoryRegistryInterface          $menuFactory,
                           FormFactoryInterface                      $formFactory,
                           DataTransferRegistryInterface             $dataTransfer,
                           Request                                   $request,
                           UuidV4                                    $id): Response
    {
        $purchasableItem = $this->findPurchasableItemOrThrow404($id);

        $updatePartItemResult = $this->updatePartItem($dataTransfer, $purchasableItem, $request);
        if ($updatePartItemResult instanceof RedirectResponse)
        {
            return $updatePartItemResult;
        }

        $updatePartVariantsResult = $this->updatePartVariants($purchasableItemVariantRepository, $menuFactory, $formFactory, $purchasableItem, $request);
        if ($updatePartVariantsResult instanceof RedirectResponse)
        {
            return $updatePartVariantsResult;
        }

        return $this->render('admin/purchasable_item/update.html.twig', array_merge($updatePartItemResult, $updatePartVariantsResult, [
            'breadcrumbs' => $this->breadcrumbs->buildUpdate($purchasableItem),
        ]));
    }

    private function updatePartItem(DataTransferRegistryInterface $dataTransfer,
                                    PurchasableItem               $purchasableItem,
                                    Request                       $request): array|RedirectResponse
    {
        $purchasableItemData = new PurchasableItemData($purchasableItem);
        $dataTransfer->fillData($purchasableItemData, $purchasableItem);

        $form = $this->createForm(PurchasableItemType::class, $purchasableItemData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($purchasableItemData, $purchasableItem);
            $this->purchasableItemRepository->savePurchasableItem($purchasableItem, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item.update');

            return $this->redirectToRoute('admin_purchasable_item_list');
        }

        return [
            'purchasable_item'      => $purchasableItem,
            'form_purchasable_item' => $form->createView(),
        ];
    }

    private function updatePartVariants(PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository,
                                        MenuTypeFactoryRegistryInterface          $menuFactory,
                                        FormFactoryInterface                      $formFactory,
                                        PurchasableItem                           $purchasableItem,
                                        Request                                   $request): array|RedirectResponse
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new PurchasableItemVariantSearchData();
        $form = $formFactory->createNamed('', PurchasableItemVariantSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new PurchasableItemVariantSearchData();
        }

        $paginator = $purchasableItemVariantRepository->getAdminPaginator($searchData, $purchasableItem, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
        ];
    }

    #[IsGranted('purchasable_item_delete')]
    #[Route('/admin/purchasable-item/{id}/delete', name: 'admin_purchasable_item_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $purchasableItem = $this->findPurchasableItemOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.purchasable_item_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->purchasableItemRepository->removePurchasableItem($purchasableItem, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item.delete');

            return $this->redirectToRoute('admin_purchasable_item_list');
        }

        return $this->render('admin/purchasable_item/delete.html.twig', [
            'purchasable_item' => $purchasableItem,
            'form_delete'      => $form->createView(),
            'breadcrumbs'      => $this->breadcrumbs->buildDelete($purchasableItem),
        ]);
    }

    private function findPurchasableItemOrThrow404(UuidV4 $id): PurchasableItem
    {
        $purchasableItem = $this->purchasableItemRepository->findOneById($id);
        if ($purchasableItem === null)
        {
            throw $this->createNotFoundException();
        }

        return $purchasableItem;
    }
}