<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\PurchasableItemVariantValueType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\PurchasableItemVariantValueBreadcrumbsInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[IsGranted('purchasable_item_update')]
class PurchasableItemVariantValueController extends AbstractController
{
    private PurchasableItemVariantValueRepositoryInterface $purchasableItemVariantValueRepository;
    private PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository;
    private PurchasableItemVariantValueBreadcrumbsInterface $breadcrumbs;

    public function __construct(PurchasableItemVariantValueRepositoryInterface  $purchasableItemVariantValueRepository,
                                PurchasableItemVariantRepositoryInterface       $purchasableItemVariantRepository,
                                PurchasableItemVariantValueBreadcrumbsInterface $breadcrumbs)
    {
        $this->purchasableItemVariantValueRepository = $purchasableItemVariantValueRepository;
        $this->purchasableItemVariantRepository = $purchasableItemVariantRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/admin/purchasable-item-variant/{id}/create-value', name: 'admin_purchasable_item_variant_value_create')]
    public function create(DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);

        $purchasableItemVariantValueData = new PurchasableItemVariantValueData(null, $purchasableItemVariant);
        $form = $this->createForm(PurchasableItemVariantValueType::class, $purchasableItemVariantValueData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant_value.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $purchasableItemVariantValue = new PurchasableItemVariantValue($purchasableItemVariantValueData->getName(), $purchasableItemVariantValueData->getPriority(), $purchasableItemVariant);
            $dataTransfer->fillEntity($purchasableItemVariantValueData, $purchasableItemVariantValue);
            $this->purchasableItemVariantValueRepository->savePurchasableItemVariantValue($purchasableItemVariantValue, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant_value.create');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant_value/update.html.twig', [
            'form_purchasable_item_variant_value' => $form,
            'breadcrumbs'                         => $this->breadcrumbs->buildCreate($purchasableItemVariant),
        ]);
    }

    #[Route('/admin/purchasable-item-variant-value/{id}/read', name: 'admin_purchasable_item_variant_value_read')]
    public function read(UuidV4 $id): Response
    {
        $purchasableItemVariantValue = $this->findPurchasableItemVariantValueOrThrow404($id);

        return $this->render('admin/purchasable_item/variant_value/read.html.twig', [
            'purchasable_item_variant_value' => $purchasableItemVariantValue,
            'breadcrumbs'                    => $this->breadcrumbs->buildRead($purchasableItemVariantValue),
        ]);
    }

    #[Route('/admin/purchasable-item-variant-value/{id}/update', name: 'admin_purchasable_item_variant_value_update')]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $purchasableItemVariantValue = $this->findPurchasableItemVariantValueOrThrow404($id);
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();

        $purchasableItemVariantValueData = new PurchasableItemVariantValueData($purchasableItemVariantValue, $purchasableItemVariant);
        $dataTransfer->fillData($purchasableItemVariantValueData, $purchasableItemVariantValue);
        $form = $this->createForm(PurchasableItemVariantValueType::class, $purchasableItemVariantValueData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant_value.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($purchasableItemVariantValueData, $purchasableItemVariantValue);
            $this->purchasableItemVariantValueRepository->savePurchasableItemVariantValue($purchasableItemVariantValue, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant_value.update');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant_value/update.html.twig', [
            'form_purchasable_item_variant_value' => $form->createView(),
            'breadcrumbs'                         => $this->breadcrumbs->buildUpdate($purchasableItemVariantValue),
        ]);
    }

    #[Route('/admin/purchasable-item-variant-value/{id}/delete', name: 'admin_purchasable_item_variant_value_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $purchasableItemVariantValue = $this->findPurchasableItemVariantValueOrThrow404($id);
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();

        if (!$this->purchasableItemVariantValueRepository->canRemovePurchasableItemVariantValue($purchasableItemVariantValue))
        {
            $this->addTransFlash('failure', 'crud.error.purchasable_item_variant_value_delete');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId()->toRfc4122(),
            ]);
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.purchasable_item_variant_value_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->purchasableItemVariantValueRepository->removePurchasableItemVariantValue($purchasableItemVariantValue, true);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant_value.delete');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant_value/delete.html.twig', [
            'purchasable_item_variant_value' => $purchasableItemVariantValue,
            'form_delete'                    => $form->createView(),
            'breadcrumbs'                    => $this->breadcrumbs->buildDelete($purchasableItemVariantValue),
        ]);
    }

    private function findPurchasableItemVariantValueOrThrow404(UuidV4 $id): PurchasableItemVariantValue
    {
        $purchasableItemVariantValue = $this->purchasableItemVariantValueRepository->findOneById($id);
        if ($purchasableItemVariantValue === null)
        {
            throw $this->createNotFoundException();
        }

        return $purchasableItemVariantValue;
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
}