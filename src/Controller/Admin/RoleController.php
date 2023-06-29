<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Role;
use App\Form\DataTransfer\Data\Admin\RoleData;
use App\Form\DataTransfer\Data\Admin\RoleSearchData;
use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use App\Form\Type\Admin\RoleSearchType;
use App\Form\Type\Admin\RoleType;
use App\Form\Type\Common\HiddenTrueType;
use App\Menu\Breadcrumbs\Admin\RoleBreadcrumbsInterface;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Repository\RoleRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class RoleController extends AbstractController
{
    private RoleRepositoryInterface $roleRepository;
    private RoleBreadcrumbsInterface $roleBreadcrumbs;

    public function __construct(RoleRepositoryInterface  $roleRepository,
                                RoleBreadcrumbsInterface $roleBreadcrumbs)
    {
        $this->roleRepository = $roleRepository;
        $this->roleBreadcrumbs = $roleBreadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("role_create") or is_granted("role_read") or 
                                         is_granted("role_update") or is_granted("role_delete")'))]
    #[Route('/admin/roles', name: 'admin_role_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new RoleSearchData();
        $form = $formFactory->createNamed('', RoleSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new RoleSearchData();
        }

        $paginator = $this->roleRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/role/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            '_breadcrumbs'      => $this->roleBreadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('role_create')]
    #[Route('/admin/role/create', name: 'admin_role_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        $roleData = new RoleData();

        $form = $this->createForm(RoleType::class, $roleData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.role.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $role = $this->roleRepository->createRole($roleData->getLabel());
            $dataTransfer->fillEntity($roleData, $role);
            $this->roleRepository->saveRole($role, true);
            $this->addTransFlash('success', 'crud.action_performed.Role.create');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/update.html.twig', [
            'form_role'    => $form->createView(),
            '_breadcrumbs' => $this->roleBreadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('role_read')]
    #[Route('/admin/role/{id}/read', name: 'admin_role_read', requirements: ['id' => '\d+'])]
    public function read(UserRepositoryInterface $userRepository, int $id): Response
    {
        $role = $this->findRoleOrThrow404($id);
        $users = $userRepository->findByRole($role);

        return $this->render('admin/role/read.html.twig', [
            'role'         => $role,
            'users'        => $users,
            '_breadcrumbs' => $this->roleBreadcrumbs->buildRead($role->getId()),
        ]);
    }

    #[IsGranted('role_update')]
    #[Route('/admin/role/{id}/update', name: 'admin_role_update', requirements: ['id' => '\d+'])]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, int $id): Response
    {
        $role = $this->findRoleOrThrow404($id);

        $roleData = new RoleData();
        $dataTransfer->fillData($roleData, $role);

        $form = $this->createForm(RoleType::class, $roleData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.role.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($roleData, $role);
            $this->roleRepository->saveRole($role, true);
            $this->addTransFlash('success', 'crud.action_performed.Role.update');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/update.html.twig', [
            'role'         => $role,
            'form_role'    => $form->createView(),
            '_breadcrumbs' => $this->roleBreadcrumbs->buildUpdate($role->getId()),
        ]);
    }

    #[IsGranted('role_delete')]
    #[Route('/admin/role/{id}/delete', name: 'admin_role_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): Response
    {
        $role = $this->findRoleOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.role_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->roleRepository->removeRole($role, true);
            $this->addTransFlash('success', 'crud.action_performed.Role.delete');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/delete.html.twig', [
            'role'         => $role,
            'form_delete'  => $form->createView(),
            '_breadcrumbs' => $this->roleBreadcrumbs->buildDelete($role->getId()),
        ]);
    }

    private function findRoleOrThrow404(int $id): Role
    {
        $role = $this->roleRepository->findOneById($id);
        if ($role === null)
        {
            throw $this->createNotFoundException();
        }

        return $role;
    }
}