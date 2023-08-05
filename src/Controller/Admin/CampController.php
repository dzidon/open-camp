<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Form\DataTransfer\Data\Admin\CampData;
use App\Form\DataTransfer\Data\Admin\CampSearchData;
use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use App\Form\Type\Admin\CampSearchType;
use App\Form\Type\Admin\CampType;
use App\Form\Type\Common\HiddenTrueType;
use App\Menu\Breadcrumbs\Admin\CampBreadcrumbsInterface;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Model\Entity\Camp;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class CampController extends AbstractController
{
    private CampRepositoryInterface $campRepository;
    private CampBreadcrumbsInterface $campBreadcrumbs;

    public function __construct(CampRepositoryInterface $campRepository,
                                CampBreadcrumbsInterface $campBreadcrumbs)
    {
        $this->campRepository = $campRepository;
        $this->campBreadcrumbs = $campBreadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("camp_create") or is_granted("camp_read") or 
                                         is_granted("camp_update") or is_granted("camp_delete")'))]
    #[Route('/admin/camps', name: 'admin_camp_list')]
    public function list(FormFactoryInterface             $formFactory,
                         MenuTypeFactoryRegistryInterface $menuFactory,
                         CampCategoryRepositoryInterface  $campCategoryRepository,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $campCategoryChoices = $campCategoryRepository->findAll();

        $searchData = new CampSearchData();
        $form = $formFactory->createNamed('', CampSearchType::class, $searchData, ['choices_camp_categories' => $campCategoryChoices]);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new CampSearchData();
        }

        $paginator = $this->campRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/camp/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            '_breadcrumbs'      => $this->campBreadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('camp_create')]
    #[Route('/admin/camp/create', name: 'admin_camp_create')]
    public function create(DataTransferRegistryInterface   $dataTransfer,
                           CampCategoryRepositoryInterface $campCategoryRepository,
                           Request                         $request): Response
    {
        $campCategoryChoices = $campCategoryRepository->findAll();

        $campData = new CampData();
        $form = $this->createForm(CampType::class, $campData, ['choices_camp_categories' => $campCategoryChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $camp = $this->campRepository->createCamp($campData->getName(), $campData->getUrlName(), $campData->getAgeMin(), $campData->getAgeMax());
            $dataTransfer->fillEntity($campData, $camp);
            $this->campRepository->saveCamp($camp, true);
            $this->addTransFlash('success', 'crud.action_performed.camp.create');

            return $this->redirectToRoute('admin_camp_list');
        }

        return $this->render('admin/camp/update.html.twig', [
            'form_camp'    => $form->createView(),
            '_breadcrumbs' => $this->campBreadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('camp_read')]
    #[Route('/admin/camp/{id}/read', name: 'admin_camp_read')]
    public function read(CampCategoryRepositoryInterface $campCategoryRepository, UuidV4 $id): Response
    {
        $camp = $this->findCampOrThrow404($id);

        // load all camp categories so that the camp category path does not trigger additional queries
        $campCategoryRepository->findAll();

        return $this->render('admin/camp/read.html.twig', [
            'camp' => $camp,
            '_breadcrumbs'  => $this->campBreadcrumbs->buildRead($camp->getId()),
        ]);
    }

    #[IsGranted('camp_update')]
    #[Route('/admin/camp/{id}/update', name: 'admin_camp_update')]
    public function update(DataTransferRegistryInterface   $dataTransfer,
                           CampCategoryRepositoryInterface $campCategoryRepository,
                           Request                         $request,
                           UuidV4                          $id): Response
    {
        $camp = $this->findCampOrThrow404($id);
        $campCategoryChoices = $campCategoryRepository->findAll();

        $campData = new CampData();
        $dataTransfer->fillData($campData, $camp);

        $form = $this->createForm(CampType::class, $campData, ['choices_camp_categories' => $campCategoryChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($campData, $camp);
            $this->campRepository->saveCamp($camp, true);
            $this->addTransFlash('success', 'crud.action_performed.camp.update');

            return $this->redirectToRoute('admin_camp_list');
        }

        return $this->render('admin/camp/update.html.twig', [
            'camp'         => $camp,
            'form_camp'    => $form->createView(),
            '_breadcrumbs' => $this->campBreadcrumbs->buildUpdate($camp->getId()),
        ]);
    }

    #[IsGranted('camp_delete')]
    #[Route('/admin/camp/{id}/delete', name: 'admin_camp_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $camp = $this->findCampOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.camp_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->campRepository->removeCamp($camp, true);
            $this->addTransFlash('success', 'crud.action_performed.camp.delete');

            return $this->redirectToRoute('admin_camp_list');
        }

        return $this->render('admin/camp/delete.html.twig', [
            'camp'          => $camp,
            'form_delete'   => $form->createView(),
            '_breadcrumbs'  => $this->campBreadcrumbs->buildDelete($camp->getId()),
        ]);
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