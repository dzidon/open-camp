<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationPurchasableItemsData;
use App\Model\Entity\Application;
use App\Model\Event\Admin\ApplicationPurchasableItem\ApplicationPurchasableItemsBulkUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Factory\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ApplicationPurchasableItemsType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationPurchasableItemsController extends AbstractController
{
    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationRepositoryInterface $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/admin/application/{id}/purchasable-items', name: 'admin_application_purchasable_items_update')]
    public function update(ApplicationPurchasableItemInstanceDataFactoryInterface $applicationPurchasableItemInstanceDataFactory,
                           EventDispatcherInterface                               $eventDispatcher,
                           DataTransferRegistryInterface                          $dataTransfer,
                           Request                                                $request,
                           UuidV4                                                 $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedAccess($application);

        if (empty($application->getApplicationPurchasableItems()))
        {
            throw $this->createNotFoundException();
        }

        $applicationPurchasableItemsData = new ApplicationPurchasableItemsData();
        $dataTransfer->fillData($applicationPurchasableItemsData, $application);
        $emptyData = $applicationPurchasableItemInstanceDataFactory->getDataCallableArrayForAdminModule($application);

        $form = $this->createForm(ApplicationPurchasableItemsType::class, $applicationPurchasableItemsData, [
            'instances_empty_data' => $emptyData,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.application_purchasable_items.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationPurchasableItemsBulkUpdateEvent($applicationPurchasableItemsData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_purchasable_item.bulk_update');

            return $this->redirectToRoute('admin_application_purchasable_items_update', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/purchasable_item/bulk_update.html.twig', [
            'application'                        => $application,
            'form_application_purchasable_items' => $form->createView(),
            'breadcrumbs'                        => $this->createBreadcrumbs([
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    private function assertIsGrantedAccess(Application $application): void
    {
        if (!$this->isGranted('application_update') && !$this->isGranted('guide_access_update', $application))
        {
            throw $this->createAccessDeniedException();
        }
    }

    private function findApplicationOrThrow404(UuidV4 $id): Application
    {
        $application = $this->applicationRepository->findOneById($id);

        if ($application === null)
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}