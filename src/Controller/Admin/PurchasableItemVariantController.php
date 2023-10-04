<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\PurchasableItemVariantCreationType;
use App\Service\Form\Type\Admin\PurchasableItemVariantType;
use App\Service\Form\Type\Admin\PurchasableItemVariantValueSearchType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\PurchasableItemVariantBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
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
    private PurchasableItemVariantBreadcrumbsInterface $breadcrumbs;

    public function __construct(PurchasableItemVariantRepositoryInterface  $purchasableItemVariantRepository,
                                PurchasableItemRepositoryInterface         $purchasableItemRepository,
                                PurchasableItemVariantBreadcrumbsInterface $breadcrumbs)
    {
        $this->purchasableItemVariantRepository = $purchasableItemVariantRepository;
        $this->purchasableItemRepository = $purchasableItemRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/admin/purchasable-item/{id}/create-variant', name: 'admin_purchasable_item_variant_create')]
    public function create(DataTransferRegistryInterface                  $dataTransfer,
                           PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository,
                           Request                                        $request,
                           UuidV4                                         $id): Response
    {
        $purchasableItem = $this->findPurchasableItemOrThrow404($id);

        $purchasableItemVariantCreationData = new PurchasableItemVariantCreationData($purchasableItem);
        $form = $this->createForm(PurchasableItemVariantCreationType::class, $purchasableItemVariantCreationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant_creation.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // variant
            $purchasableItemVariantData = $purchasableItemVariantCreationData->getPurchasableItemVariantData();
            $purchasableItemVariant = new PurchasableItemVariant($purchasableItemVariantData->getName(), $purchasableItemVariantData->getPriority(), $purchasableItem);
            $dataTransfer->fillEntity($purchasableItemVariantData, $purchasableItemVariant);

            // values
            $purchasableItemVariantValuesData = $purchasableItemVariantCreationData->getPurchasableItemVariantValuesData();
            foreach ($purchasableItemVariantValuesData as $purchasableItemVariantValueData)
            {
                $purchasableItemVariantValue = new PurchasableItemVariantValue($purchasableItemVariantValueData->getName(), $purchasableItemVariantValueData->getPriority(), $purchasableItemVariant);
                $dataTransfer->fillEntity($purchasableItemVariantValueData, $purchasableItemVariantValue);
                $purchasableItemVariantValueRepository->savePurchasableItemVariantValue($purchasableItemVariantValue, false);
            }

            $this->purchasableItemVariantRepository->savePurchasableItemVariant($purchasableItemVariant, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant.create');

            return $this->redirectToRoute('admin_purchasable_item_update', [
                'id' => $purchasableItem->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant/update.html.twig', [
            'form_purchasable_item_variant' => $form,
            'breadcrumbs'                   => $this->breadcrumbs->buildCreate($purchasableItem),
        ]);
    }

    #[Route('/admin/purchasable-item-variant/{id}/read', name: 'admin_purchasable_item_variant_read')]
    public function read(PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository, UuidV4 $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);
        $purchasableItemVariantValues = $purchasableItemVariantValueRepository->findByPurchasableItemVariant($purchasableItemVariant);

        return $this->render('admin/purchasable_item/variant/read.html.twig', [
            'purchasable_item_variant'        => $purchasableItemVariant,
            'purchasable_item_variant_values' => $purchasableItemVariantValues,
            'breadcrumbs'                     => $this->breadcrumbs->buildRead($purchasableItemVariant),
        ]);
    }

    #[Route('/admin/purchasable-item-variant/{id}/update', name: 'admin_purchasable_item_variant_update')]
    public function update(PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository,
                           MenuTypeFactoryRegistryInterface               $menuFactory,
                           FormFactoryInterface                           $formFactory,
                           DataTransferRegistryInterface                  $dataTransfer,
                           Request                                        $request,
                           UuidV4                                         $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);

        $updatePartVariantResult = $this->updatePartVariant($dataTransfer, $purchasableItemVariant, $request);
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
            'breadcrumbs' => $this->breadcrumbs->buildUpdate($purchasableItemVariant),
        ]));
    }

    private function updatePartVariant(DataTransferRegistryInterface $dataTransfer,
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
            $dataTransfer->fillEntity($purchasableItemVariantData, $purchasableItemVariant);
            $this->purchasableItemVariantRepository->savePurchasableItemVariant($purchasableItemVariant, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant.update');

            return $this->redirectToRoute('admin_purchasable_item_update', [
                'id' => $purchasableItem->getId()->toRfc4122(),
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
    public function delete(Request $request, UuidV4 $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.purchasable_item_variant_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->purchasableItemVariantRepository->removePurchasableItemVariant($purchasableItemVariant, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant.delete');
            $purchasableItem = $purchasableItemVariant->getPurchasableItem();

            return $this->redirectToRoute('admin_purchasable_item_update', [
                'id' => $purchasableItem->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant/delete.html.twig', [
            'purchasable_item_variant' => $purchasableItemVariant,
            'form_delete'              => $form->createView(),
            'breadcrumbs'              => $this->breadcrumbs->buildDelete($purchasableItemVariant),
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