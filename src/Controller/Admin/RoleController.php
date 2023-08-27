<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\RoleData;
use App\Library\Data\Admin\RoleSearchData;
use App\Model\Entity\Role;
use App\Model\Repository\PermissionRepositoryInterface;
use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\RoleSearchType;
use App\Service\Form\Type\Admin\RoleType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\RoleBreadcrumbsInterface;
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
            'breadcrumbs'       => $this->roleBreadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('role_create')]
    #[Route('/admin/role/create', name: 'admin_role_create')]
    public function create(DataTransferRegistryInterface $dataTransfer,
                           PermissionRepositoryInterface $permissionRepository,
                           Request                       $request): Response
    {
        $roleData = new RoleData();
        $permissionChoices = $permissionRepository->findAll();

        $form = $this->createForm(RoleType::class, $roleData, ['choices_permissions' => $permissionChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.role.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $role = $this->roleRepository->createRole($roleData->getLabel());
            $dataTransfer->fillEntity($roleData, $role);
            $this->roleRepository->saveRole($role, true);
            $this->addTransFlash('success', 'crud.action_performed.role.create');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/update.html.twig', [
            'form_role'   => $form->createView(),
            'breadcrumbs' => $this->roleBreadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('role_read')]
    #[Route('/admin/role/{id}/read', name: 'admin_role_read')]
    public function read(UserRepositoryInterface $userRepository, UuidV4 $id): Response
    {
        $role = $this->findRoleOrThrow404($id);
        $users = $userRepository->findByRole($role);

        return $this->render('admin/role/read.html.twig', [
            'role'        => $role,
            'users'       => $users,
            'breadcrumbs' => $this->roleBreadcrumbs->buildRead($role->getId()),
        ]);
    }

    #[IsGranted('role_update')]
    #[Route('/admin/role/{id}/update', name: 'admin_role_update')]
    public function update(DataTransferRegistryInterface $dataTransfer,
                           PermissionRepositoryInterface $permissionRepository,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $role = $this->findRoleOrThrow404($id);
        $permissionChoices = $permissionRepository->findAll();

        $roleData = new RoleData();
        $dataTransfer->fillData($roleData, $role);

        $form = $this->createForm(RoleType::class, $roleData, ['choices_permissions' => $permissionChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.role.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($roleData, $role);
            $this->roleRepository->saveRole($role, true);
            $this->addTransFlash('success', 'crud.action_performed.role.update');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/update.html.twig', [
            'role'        => $role,
            'form_role'   => $form->createView(),
            'breadcrumbs' => $this->roleBreadcrumbs->buildUpdate($role->getId()),
        ]);
    }

    #[IsGranted('role_delete')]
    #[Route('/admin/role/{id}/delete', name: 'admin_role_delete')]
    public function delete(Request $request, UuidV4 $id): Response
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
            $this->addTransFlash('success', 'crud.action_performed.role.delete');

            return $this->redirectToRoute('admin_role_list');
        }

        return $this->render('admin/role/delete.html.twig', [
            'role'        => $role,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->roleBreadcrumbs->buildDelete($role->getId()),
        ]);
    }

    private function findRoleOrThrow404(UuidV4 $id): Role
    {
        $role = $this->roleRepository->findOneById($id);
        if ($role === null)
        {
            throw $this->createNotFoundException();
        }

        return $role;
    }
}