<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\FormFieldData;
use App\Library\Data\Admin\FormFieldSearchData;
use App\Model\Entity\FormField;
use App\Model\Event\Admin\FormField\FormFieldCreateEvent;
use App\Model\Event\Admin\FormField\FormFieldDeleteEvent;
use App\Model\Event\Admin\FormField\FormFieldUpdateEvent;
use App\Model\Repository\FormFieldRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\FormFieldSearchType;
use App\Service\Form\Type\Admin\FormFieldType;
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
class FormFieldController extends AbstractController
{
    private FormFieldRepositoryInterface $formFieldRepository;

    public function __construct(FormFieldRepositoryInterface  $formFieldRepository)
    {
        $this->formFieldRepository = $formFieldRepository;
    }

    #[IsGranted(new Expression('is_granted("form_field", "any_admin_permission")'))]
    #[Route('/admin/form-fields', name: 'admin_form_field_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new FormFieldSearchData();
        $form = $formFactory->createNamed('', FormFieldSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new FormFieldSearchData();
        }

        $paginator = $this->formFieldRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/form_field/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('form_field_create')]
    #[Route('/admin/form-field/create', name: 'admin_form_field_create')]
    public function create(EventDispatcherInterface $eventDispatcher,
                           Request                  $request): Response
    {
        $formFieldData = new FormFieldData(null, true);

        $form = $this->createForm(FormFieldType::class, $formFieldData, [
            'enable_items_option_validation' => true,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.form_field.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new FormFieldCreateEvent($formFieldData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.form_field.create');

            return $this->redirectToRoute('admin_form_field_list');
        }

        return $this->render('admin/form_field/update.html.twig', [
            'form_form_field' => $form->createView(),
            'breadcrumbs'     => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('form_field_read')]
    #[Route('/admin/form-field/{id}/read', name: 'admin_form_field_read')]
    public function read(UuidV4 $id): Response
    {
        $formField = $this->findFormFieldOrThrow404($id);

        return $this->render('admin/form_field/read.html.twig', [
            'form_field'   => $formField,
            'breadcrumbs'  => $this->createBreadcrumbs([
                'form_field' => $formField,
            ]),
        ]);
    }

    #[IsGranted('form_field_update')]
    #[Route('/admin/form-field/{id}/update', name: 'admin_form_field_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $formField = $this->findFormFieldOrThrow404($id);

        $formFieldData = new FormFieldData($formField, true);
        $dataTransfer->fillData($formFieldData, $formField);

        $form = $this->createForm(FormFieldType::class, $formFieldData, [
            'enable_items_option_validation' => true,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.form_field.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new FormFieldUpdateEvent($formFieldData, $formField);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.form_field.update');

            return $this->redirectToRoute('admin_form_field_list');
        }

        return $this->render('admin/form_field/update.html.twig', [
            'form_field'      => $formField,
            'form_form_field' => $form->createView(),
            'breadcrumbs'     => $this->createBreadcrumbs([
                'form_field' => $formField,
            ]),
        ]);
    }

    #[IsGranted('form_field_delete')]
    #[Route('/admin/form-field/{id}/delete', name: 'admin_form_field_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $formField = $this->findFormFieldOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.form_field_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new FormFieldDeleteEvent($formField);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.form_field.delete');

            return $this->redirectToRoute('admin_form_field_list');
        }

        return $this->render('admin/form_field/delete.html.twig', [
            'form_field'  => $formField,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'form_field' => $formField,
            ]),
        ]);
    }

    private function findFormFieldOrThrow404(UuidV4 $id): FormField
    {
        $formField = $this->formFieldRepository->findOneById($id);
        if ($formField === null)
        {
            throw $this->createNotFoundException();
        }

        return $formField;
    }
}