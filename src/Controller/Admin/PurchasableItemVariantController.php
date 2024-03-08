<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreateEvent;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantDeleteEvent;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantUpdateEvent;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\PurchasableItemVariantCreationType;
use App\Service\Form\Type\Admin\PurchasableItemVariantType;
use App\Service\Form\Type\Admin\PurchasableItemVariantValueSearchType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[IsGranted('purchasable_item_update')]
class PurchasableItemVariantController extends AbstractController
{
    private PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository;
    private PurchasableItemRepositoryInterface $purchasableItemRepository;

    public function __construct(PurchasableItemVariantRepositoryInterface  $purchasableItemVariantRepository,
                                PurchasableItemRepositoryInterface         $purchasableItemRepository)
    {
        $this->purchasableItemVariantRepository = $purchasableItemVariantRepository;
        $this->purchasableItemRepository = $purchasableItemRepository;
    }

    #[Route('/admin/purchasable-item/{id}/create-variant', name: 'admin_purchasable_item_variant_create')]
    public function create(EventDispatcherInterface $eventDispatcher,
                           Request                  $request,
                           UuidV4                   $id): Response
    {
        $purchasableItem = $this->findPurchasableItemOrThrow404($id);

        $purchasableItemVariantCreationData = new PurchasableItemVariantCreationData($purchasableItem);
        $form = $this->createForm(PurchasableItemVariantCreationType::class, $purchasableItemVariantCreationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant_creation.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PurchasableItemVariantCreateEvent($purchasableItemVariantCreationData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant.create');

            return $this->redirectToRoute('admin_purchasable_item_update', [
                'id' => $purchasableItem->getId(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant/update.html.twig', [
            'form_purchasable_item_variant' => $form->createView(),
            'breadcrumbs'                   => $this->createBreadcrumbs([
                'purchasable_item' => $purchasableItem,
            ]),
        ]);
    }

    #[Route('/admin/purchasable-item-variant/{id}/read', name: 'admin_purchasable_item_variant_read')]
    public function read(UuidV4 $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        return $this->render('admin/purchasable_item/variant/read.html.twig', [
            'purchasable_item_variant' => $purchasableItemVariant,
            'breadcrumbs'              => $this->createBreadcrumbs([
                'purchasable_item'         => $purchasableItem,
                'purchasable_item_variant' => $purchasableItemVariant,
            ]),
        ]);
    }

    #[Route('/admin/purchasable-item-variant/{id}/update', name: 'admin_purchasable_item_variant_update')]
    public function update(EventDispatcherInterface                       $eventDispatcher,
                           PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository,
                           MenuTypeFactoryRegistryInterface               $menuFactory,
                           FormFactoryInterface                           $formFactory,
                           DataTransferRegistryInterface                  $dataTransfer,
                           Request                                        $request,
                           UuidV4                                         $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        $updatePartVariantResult = $this->updatePartVariant($eventDispatcher, $dataTransfer, $purchasableItemVariant, $request);
        if ($updatePartVariantResult instanceof RedirectResponse)
        {
            return $updatePartVariantResult;
        }

        $updatePartValuesResult = $this->updatePartValues($purchasableItemVariantValueRepository, $menuFactory, $formFactory, $purchasableItemVariant, $request);
        if ($updatePartValuesResult instanceof RedirectResponse)
        {
            return $updatePartValuesResult;
        }

        return $this->render('admin/purchasable_item/variant/update.html.twig', array_merge($updatePartVariantResult, $updatePartValuesResult, [
            'breadcrumbs' => $this->createBreadcrumbs([
                'purchasable_item'         => $purchasableItem,
                'purchasable_item_variant' => $purchasableItemVariant,
            ]),
        ]));
    }

    private function updatePartVariant(EventDispatcherInterface      $eventDispatcher,
                                       DataTransferRegistryInterface $dataTransfer,
                                       PurchasableItemVariant        $purchasableItemVariant,
                                       Request                       $request): array|RedirectResponse
    {
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();
        $purchasableItemVariantData = new PurchasableItemVariantData($purchasableItem, $purchasableItemVariant);
        $dataTransfer->fillData($purchasableItemVariantData, $purchasableItemVariant);

        $form = $this->createForm(PurchasableItemVariantType::class, $purchasableItemVariantData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PurchasableItemVariantUpdateEvent($purchasableItemVariantData, $purchasableItemVariant);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant.update');

            return $this->redirectToRoute('admin_purchasable_item_update', [
                'id' => $purchasableItem->getId(),
            ]);
        }

        return [
            'purchasable_item_variant'      => $purchasableItemVariant,
            'form_purchasable_item_variant' => $form->createView(),
        ];
    }

    private function updatePartValues(PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository,
                                      MenuTypeFactoryRegistryInterface               $menuFactory,
                                      FormFactoryInterface                           $formFactory,
                                      PurchasableItemVariant                         $purchasableItemVariant,
                                      Request                                        $request): array|RedirectResponse
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new PurchasableItemVariantValueSearchData();
        $form = $formFactory->createNamed('', PurchasableItemVariantValueSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new PurchasableItemVariantValueSearchData();
        }

        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($searchData, $purchasableItemVariant, $page, 20);
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

    #[Route('/admin/purchasable-item-variant/{id}/delete', name: 'admin_purchasable_item_variant_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.purchasable_item_variant_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PurchasableItemVariantDeleteEvent($purchasableItemVariant);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant.delete');
            $purchasableItem = $purchasableItemVariant->getPurchasableItem();

            return $this->redirectToRoute('admin_purchasable_item_update', [
                'id' => $purchasableItem->getId(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant/delete.html.twig', [
            'purchasable_item_variant' => $purchasableItemVariant,
            'form_delete'              => $form->createView(),
            'breadcrumbs'              => $this->createBreadcrumbs([
                'purchasable_item'         => $purchasableItem,
                'purchasable_item_variant' => $purchasableItemVariant,
            ]),
        ]);
    }

    private function findPurchasableItemVariantOrThrow404(UuidV4 $id): PurchasableItemVariant
    {
        $purchasableItemVariant = $this->purchasableItemVariantRepository->findOneById($id);
        if ($purchasableItemVariant === null)
        {
            throw $this->createNotFoundException();
        }

        return $purchasableItemVariant;
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