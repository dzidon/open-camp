<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\CampCategoryData;
use App\Model\Entity\CampCategory;
use App\Model\Event\Admin\CampCategory\CampCategoryCreateEvent;
use App\Model\Event\Admin\CampCategory\CampCategoryDeleteEvent;
use App\Model\Event\Admin\CampCategory\CampCategoryUpdateEvent;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\CampCategoryType;
use App\Service\Form\Type\Common\HiddenTrueType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class CampCategoryController extends AbstractController
{
    private CampCategoryRepositoryInterface $campCategoryRepository;

    public function __construct(CampCategoryRepositoryInterface $campCategoryRepository)
    {
        $this->campCategoryRepository = $campCategoryRepository;
    }

    #[IsGranted(new Expression('is_granted("camp_category", "any_admin_permission")'))]
    #[Route('/admin/camp-categories', name: 'admin_camp_category_list')]
    public function list(): Response
    {
        $rootCategories = $this->campCategoryRepository->findRoots();

        return $this->render('admin/camp_category/list.html.twig', [
            'root_categories' => $rootCategories,
            'breadcrumbs'     => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('camp_category_create')]
    #[Route('/admin/camp-category/create', name: 'admin_camp_category_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $campCategoryData = new CampCategoryData();
        $parentChoices = $this->campCategoryRepository->findAll();

        $form = $this->createForm(CampCategoryType::class, $campCategoryData, ['choices_camp_categories' => $parentChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_category.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new CampCategoryCreateEvent($campCategoryData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.camp_category.create');

            return $this->redirectToRoute('admin_camp_category_list');
        }

        return $this->render('admin/camp_category/update.html.twig', [
            'form_camp_category' => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('camp_category_read')]
    #[Route('/admin/camp-category/{id}/read', name: 'admin_camp_category_read')]
    public function read(UuidV4 $id): Response
    {
        $campCategory = $this->findCampCategoryOrThrow404($id);

        return $this->render('admin/camp_category/read.html.twig', [
            'camp_category' => $campCategory,
            'breadcrumbs'   => $this->createBreadcrumbs([
                'camp_category' => $campCategory,
            ]),
        ]);
    }

    #[IsGranted('camp_category_update')]
    #[Route('/admin/camp-category/{id}/update', name: 'admin_camp_category_update')]
    public function update(EventDispatcherInterface $eventDispatcher, DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $campCategory = $this->findCampCategoryOrThrow404($id);
        $parentChoices = $this->campCategoryRepository->findPossibleParents($campCategory);

        $campCategoryData = new CampCategoryData($campCategory);
        $dataTransfer->fillData($campCategoryData, $campCategory);

        $form = $this->createForm(CampCategoryType::class, $campCategoryData, ['choices_camp_categories' => $parentChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_category.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new CampCategoryUpdateEvent($campCategoryData, $campCategory);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.camp_category.update');

            return $this->redirectToRoute('admin_camp_category_list');
        }

        return $this->render('admin/camp_category/update.html.twig', [
            'camp_category'      => $campCategory,
            'form_camp_category' => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs([
                'camp_category' => $campCategory,
            ]),
        ]);
    }

    #[IsGranted('camp_category_delete')]
    #[Route('/admin/camp-category/{id}/delete', name: 'admin_camp_category_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $campCategory = $this->findCampCategoryOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.camp_category_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new CampCategoryDeleteEvent($campCategory);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.camp_category.delete');

            return $this->redirectToRoute('admin_camp_category_list');
        }

        return $this->render('admin/camp_category/delete.html.twig', [
            'camp_category' => $campCategory,
            'form_delete'   => $form->createView(),
            'breadcrumbs'   => $this->createBreadcrumbs([
                'camp_category' => $campCategory,
            ]),
        ]);
    }

    private function findCampCategoryOrThrow404(UuidV4 $id): CampCategory
    {
        $campCategory = $this->campCategoryRepository->findOneById($id);
        if ($campCategory === null)
        {
            throw $this->createNotFoundException();
        }

        return $campCategory;
    }
}