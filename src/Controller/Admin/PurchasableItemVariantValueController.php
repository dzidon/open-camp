<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreateEvent;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueDeleteEvent;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueUpdateEvent;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\PurchasableItemVariantValueType;
use App\Service\Form\Type\Common\HiddenTrueType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    public function __construct(PurchasableItemVariantValueRepositoryInterface  $purchasableItemVariantValueRepository,
                                PurchasableItemVariantRepositoryInterface       $purchasableItemVariantRepository)
    {
        $this->purchasableItemVariantValueRepository = $purchasableItemVariantValueRepository;
        $this->purchasableItemVariantRepository = $purchasableItemVariantRepository;
    }

    #[Route('/admin/purchasable-item-variant/{id}/create-value', name: 'admin_purchasable_item_variant_value_create')]
    public function create(EventDispatcherInterface $eventDispatcher,
                           Request                  $request,
                           UuidV4                   $id): Response
    {
        $purchasableItemVariant = $this->findPurchasableItemVariantOrThrow404($id);
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        $purchasableItemVariantValueData = new PurchasableItemVariantValueData(null, $purchasableItemVariant);
        $form = $this->createForm(PurchasableItemVariantValueType::class, $purchasableItemVariantValueData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant_value.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PurchasableItemVariantValueCreateEvent($purchasableItemVariantValueData, $purchasableItemVariant);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant_value.create');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant_value/update.html.twig', [
            'form_purchasable_item_variant_value' => $form,
            'breadcrumbs'                         => $this->createBreadcrumbs([
                'purchasable_item'         => $purchasableItem,
                'purchasable_item_variant' => $purchasableItemVariant,
            ]),
        ]);
    }

    #[Route('/admin/purchasable-item-variant-value/{id}/read', name: 'admin_purchasable_item_variant_value_read')]
    public function read(UuidV4 $id): Response
    {
        $purchasableItemVariantValue = $this->findPurchasableItemVariantValueOrThrow404($id);
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        return $this->render('admin/purchasable_item/variant_value/read.html.twig', [
            'purchasable_item_variant_value' => $purchasableItemVariantValue,
            'breadcrumbs'                    => $this->createBreadcrumbs([
                'purchasable_item'               => $purchasableItem,
                'purchasable_item_variant'       => $purchasableItemVariant,
                'purchasable_item_variant_value' => $purchasableItemVariantValue,
            ]),
        ]);
    }

    #[Route('/admin/purchasable-item-variant-value/{id}/update', name: 'admin_purchasable_item_variant_value_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $purchasableItemVariantValue = $this->findPurchasableItemVariantValueOrThrow404($id);
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        $purchasableItemVariantValueData = new PurchasableItemVariantValueData($purchasableItemVariantValue, $purchasableItemVariant);
        $dataTransfer->fillData($purchasableItemVariantValueData, $purchasableItemVariantValue);
        $form = $this->createForm(PurchasableItemVariantValueType::class, $purchasableItemVariantValueData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.purchasable_item_variant_value.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new PurchasableItemVariantValueUpdateEvent($purchasableItemVariantValueData, $purchasableItemVariantValue);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant_value.update');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant_value/update.html.twig', [
            'form_purchasable_item_variant_value' => $form->createView(),
            'breadcrumbs'                         => $this->createBreadcrumbs([
                'purchasable_item'               => $purchasableItem,
                'purchasable_item_variant'       => $purchasableItemVariant,
                'purchasable_item_variant_value' => $purchasableItemVariantValue,
            ]),
        ]);
    }

    #[Route('/admin/purchasable-item-variant-value/{id}/delete', name: 'admin_purchasable_item_variant_value_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $purchasableItemVariantValue = $this->findPurchasableItemVariantValueOrThrow404($id);
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();
        $purchasableItem = $purchasableItemVariant->getPurchasableItem();

        if (!$this->purchasableItemVariantValueRepository->canRemovePurchasableItemVariantValue($purchasableItemVariantValue))
        {
            $this->addTransFlash('failure', 'crud.error.purchasable_item_variant_value_delete');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId(),
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
            $event = new PurchasableItemVariantValueDeleteEvent($purchasableItemVariantValue);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.purchasable_item_variant_value.delete');

            return $this->redirectToRoute('admin_purchasable_item_variant_update', [
                'id' => $purchasableItemVariant->getId(),
            ]);
        }

        return $this->render('admin/purchasable_item/variant_value/delete.html.twig', [
            'purchasable_item_variant_value' => $purchasableItemVariantValue,
            'form_delete'                    => $form->createView(),
            'breadcrumbs'                    => $this->createBreadcrumbs([
                'purchasable_item'               => $purchasableItem,
                'purchasable_item_variant'       => $purchasableItemVariant,
                'purchasable_item_variant_value' => $purchasableItemVariantValue,
            ]),
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