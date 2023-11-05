<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\FormFieldData;
use App\Library\Data\Admin\FormFieldSearchData;
use App\Model\Entity\FormField;
use App\Model\Repository\FormFieldRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\FormFieldSearchType;
use App\Service\Form\Type\Admin\FormFieldType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\FormFieldBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
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
    private FormFieldBreadcrumbsInterface $breadcrumbs;

    public function __construct(FormFieldRepositoryInterface  $formFieldRepository,
                                FormFieldBreadcrumbsInterface $breadcrumbs)
    {
        $this->formFieldRepository = $formFieldRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("form_field_create") or is_granted("form_field_read") or 
                                         is_granted("form_field_update") or is_granted("form_field_delete")'))]
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
            'breadcrumbs'       => $this->breadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('form_field_create')]
    #[Route('/admin/form-field/create', name: 'admin_form_field_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        $formFieldData = new FormFieldData(null, true);

        $form = $this->createForm(FormFieldType::class, $formFieldData, [
            'enable_items_option_validation' => true,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.form_field.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $formField = new FormField($formFieldData->getName(), $formFieldData->getType(), $formFieldData->getLabel(), $formFieldData->getOptions());
            $dataTransfer->fillEntity($formFieldData, $formField);
            $this->formFieldRepository->saveFormField($formField, true);
            $this->addTransFlash('success', 'crud.action_performed.form_field.create');

            return $this->redirectToRoute('admin_form_field_list');
        }

        return $this->render('admin/form_field/update.html.twig', [
            'form_form_field' => $form->createView(),
            'breadcrumbs'     => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('form_field_read')]
    #[Route('/admin/form-field/{id}/read', name: 'admin_form_field_read')]
    public function read(UuidV4 $id): Response
    {
        $formField = $this->findFormFieldOrThrow404($id);

        return $this->render('admin/form_field/read.html.twig', [
            'form_field'   => $formField,
            'breadcrumbs'  => $this->breadcrumbs->buildRead($formField),
        ]);
    }

    #[IsGranted('form_field_update')]
    #[Route('/admin/form-field/{id}/update', name: 'admin_form_field_update')]
    public function update(DataTransferRegistryInterface $dataTransfer,
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
            $dataTransfer->fillEntity($formFieldData, $formField);
            $this->formFieldRepository->saveFormField($formField, true);
            $this->addTransFlash('success', 'crud.action_performed.form_field.update');

            return $this->redirectToRoute('admin_form_field_list');
        }

        return $this->render('admin/form_field/update.html.twig', [
            'form_field'      => $formField,
            'form_form_field' => $form->createView(),
            'breadcrumbs'     => $this->breadcrumbs->buildUpdate($formField),
        ]);
    }

    #[IsGranted('form_field_delete')]
    #[Route('/admin/form-field/{id}/delete', name: 'admin_form_field_delete')]
    public function delete(Request $request, UuidV4 $id): Response
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
            $this->formFieldRepository->removeFormField($formField, true);
            $this->addTransFlash('success', 'crud.action_performed.form_field.delete');

            return $this->redirectToRoute('admin_form_field_list');
        }

        return $this->render('admin/form_field/delete.html.twig', [
            'form_field'  => $formField,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildDelete($formField),
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