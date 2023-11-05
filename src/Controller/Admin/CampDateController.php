<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateSearchData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Model\Repository\FormFieldRepositoryInterface;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\CampDateSearchType;
use App\Service\Form\Type\Admin\CampDateType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\CampDateBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[IsGranted('camp_update')]
class CampDateController extends AbstractController
{
    private CampDateRepositoryInterface $campDateRepository;
    private CampRepositoryInterface $campRepository;
    private CampDateBreadcrumbsInterface $breadcrumbs;

    public function __construct(CampDateRepositoryInterface  $campDateRepository,
                                CampRepositoryInterface      $campRepository,
                                CampDateBreadcrumbsInterface $breadcrumbs)
    {
        $this->campDateRepository = $campDateRepository;
        $this->campRepository = $campRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/admin/camp/{id}/dates', name: 'admin_camp_date_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request,
                         UuidV4                           $id): Response
    {
        $camp = $this->findCampOrThrow404($id);
        $page = (int) $request->query->get('page', 1);

        $searchData = new CampDateSearchData();
        $form = $formFactory->createNamed('', CampDateSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new CampDateSearchData();
        }

        $paginator = $this->campDateRepository->getAdminPaginator($searchData, $camp, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        return $this->render('admin/camp/date/list.html.twig', [
            'camp'              => $camp,
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->breadcrumbs->buildList($camp),
        ]);
    }

    #[Route('/admin/camp/{id}/create-date', name: 'admin_camp_date_create')]
    public function create(DataTransferRegistryInterface       $dataTransfer,
                           TripLocationPathRepositoryInterface $tripLocationPathRepository,
                           FormFieldRepositoryInterface        $formFieldRepository,
                           AttachmentConfigRepositoryInterface $attachmentConfigRepository,
                           PurchasableItemRepositoryInterface  $purchasableItemRepository,
                           Request                             $request,
                           UuidV4                              $id): Response
    {
        $camp = $this->findCampOrThrow404($id);

        $campDateData = new CampDateData($camp);
        $form = $this->createForm(CampDateType::class, $campDateData, [
            'choices_trip_location_paths' => $tripLocationPathRepository->findAll(),
            'choices_form_fields'         => $formFieldRepository->findAll(),
            'choices_attachment_configs'  => $attachmentConfigRepository->findAll(),
            'choices_purchasable_items'   => $purchasableItemRepository->findAll(),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_date.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $campDate = new CampDate($campDateData->getStartAt(), $campDateData->getEndAt(), $campDateData->getPrice(), $campDateData->getCapacity(), $camp);
            $dataTransfer->fillEntity($campDateData, $campDate);
            $this->campDateRepository->saveCampDate($campDate, true);
            $this->addTransFlash('success', 'crud.action_performed.camp_date.create');

            return $this->redirectToRoute('admin_camp_date_list', [
                'id' => $camp->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/camp/date/update.html.twig', [
            'camp'           => $camp,
            'form_camp_date' => $form,
            'breadcrumbs'    => $this->breadcrumbs->buildCreate($camp),
        ]);
    }

    #[Route('/admin/camp-date/{id}/read', name: 'admin_camp_date_read')]
    public function read(UuidV4 $id): Response
    {
        $campDate = $this->findCampDateOrThrow404($id);
        $camp = $campDate->getCamp();

        return $this->render('admin/camp/date/read.html.twig', [
            'camp'         => $camp,
            'camp_date'    => $campDate,
            'breadcrumbs'  => $this->breadcrumbs->buildRead($campDate),
        ]);
    }

    #[Route('/admin/camp-date/{id}/update', name: 'admin_camp_date_update')]
    public function update(DataTransferRegistryInterface       $dataTransfer,
                           TripLocationPathRepositoryInterface $tripLocationPathRepository,
                           FormFieldRepositoryInterface        $formFieldRepository,
                           AttachmentConfigRepositoryInterface $attachmentConfigRepository,
                           PurchasableItemRepositoryInterface  $purchasableItemRepository,
                           Request                             $request,
                           UuidV4                              $id): Response
    {
        $campDate = $this->findCampDateOrThrow404($id);
        $camp = $campDate->getCamp();

        $campDateData = new CampDateData($camp, $campDate);
        $dataTransfer->fillData($campDateData, $campDate);

        $form = $this->createForm(CampDateType::class, $campDateData, [
            'choices_trip_location_paths' => $tripLocationPathRepository->findAll(),
            'choices_form_fields'         => $formFieldRepository->findAll(),
            'choices_attachment_configs'  => $attachmentConfigRepository->findAll(),
            'choices_purchasable_items'   => $purchasableItemRepository->findAll(),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_date.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($campDateData, $campDate);
            $this->campDateRepository->saveCampDate($campDate, true);
            $this->addTransFlash('success', 'crud.action_performed.camp_date.update');

            return $this->redirectToRoute('admin_camp_date_list', [
                'id' => $camp->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/camp/date/update.html.twig', [
            'camp'           => $camp,
            'camp_date'      => $campDate,
            'form_camp_date' => $form->createView(),
            'breadcrumbs'    => $this->breadcrumbs->buildUpdate($campDate),
        ]);
    }

    #[Route('/admin/camp-date/{id}/delete', name: 'admin_camp_date_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $campDate = $this->findCampDateOrThrow404($id);
        $camp = $campDate->getCamp();

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.camp_date_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->campDateRepository->removeCampDate($campDate, true);
            $this->addTransFlash('success', 'crud.action_performed.camp_date.delete');

            return $this->redirectToRoute('admin_camp_date_list', [
                'id' => $camp->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/camp/date/delete.html.twig', [
            'camp'         => $camp,
            'camp_date'    => $campDate,
            'form_delete'  => $form->createView(),
            'breadcrumbs'  => $this->breadcrumbs->buildDelete($campDate),
        ]);
    }

    private function findCampDateOrThrow404(UuidV4 $id): CampDate
    {
        $campDate = $this->campDateRepository->findOneById($id);
        if ($campDate === null)
        {
            throw $this->createNotFoundException();
        }

        return $campDate;
    }

    private function findCampOrThrow404(UuidV4 $id): Camp
    {
        $camp = $this->campRepository->findOneById($id);
        if ($camp === null)
        {
            throw $this->createNotFoundException();
        }

        return $camp;
    }
}