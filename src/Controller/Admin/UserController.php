<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PlainPasswordData;
use App\Library\Data\Admin\UserData;
use App\Library\Data\Admin\UserSearchData;
use App\Model\Entity\User;
use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\RepeatedPasswordType;
use App\Service\Form\Type\Admin\UserSearchType;
use App\Service\Form\Type\Admin\UserType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\UserBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class UserController extends AbstractController
{
    private UserRepositoryInterface $userRepository;
    private UserBreadcrumbsInterface $userBreadcrumbs;

    public function __construct(UserRepositoryInterface  $userRepository,
                                UserBreadcrumbsInterface $userBreadcrumbs)
    {
        $this->userRepository = $userRepository;
        $this->userBreadcrumbs = $userBreadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("user_create") or is_granted("user_read") or 
                                         is_granted("user_update") or is_granted("user_delete") or
                                         is_granted("user_update_role")'))]
    #[Route('/admin/users', name: 'admin_user_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         RoleRepositoryInterface          $roleRepository,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new UserSearchData();
        $roleChoices = $roleRepository->findAll();
        $form = $formFactory->createNamed('', UserSearchType::class, $searchData, ['choices_roles' => $roleChoices]);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new UserSearchData();
        }

        $paginator = $this->userRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/user/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            '_breadcrumbs'      => $this->userBreadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('user_create')]
    #[Route('/admin/user/create', name: 'admin_user_create')]
    public function create(DataTransferRegistryInterface $dataTransfer,
                           RoleRepositoryInterface       $roleRepository,
                           Request                       $request): Response
    {
        $userData = new UserData($this->getParameter('app.eu_business_data'));

        $roleChoices = $roleRepository->findAll();
        $form = $this->createForm(UserType::class, $userData, ['choices_roles' => $roleChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.user.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $this->userRepository->createUser($userData->getEmail());
            $dataTransfer->fillEntity($userData, $user);
            $this->userRepository->saveUser($user, true);
            $this->addTransFlash('success', 'crud.action_performed.user.create');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/update.html.twig', [
            'form_user'    => $form->createView(),
            '_breadcrumbs' => $this->userBreadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('user_read')]
    #[Route('/admin/user/{id}/read', name: 'admin_user_read')]
    public function read(UuidV4 $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        return $this->render('admin/user/read.html.twig', [
            'user'         => $user,
            '_breadcrumbs' => $this->userBreadcrumbs->buildRead($user->getId()),
        ]);
    }

    #[IsGranted(new Expression('is_granted("user_update") or is_granted("user_update_role")'))]
    #[Route('/admin/user/{id}/update', name: 'admin_user_update')]
    public function update(DataTransferRegistryInterface $dataTransfer,
                           RoleRepositoryInterface       $roleRepository,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        $userData = new UserData($this->getParameter('app.eu_business_data'));
        $dataTransfer->fillData($userData, $user);

        $roleChoices = $roleRepository->findAll();
        $form = $this->createForm(UserType::class, $userData, ['choices_roles' => $roleChoices]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.user.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($userData, $user);
            $this->userRepository->saveUser($user, true);
            $this->addTransFlash('success', 'crud.action_performed.user.update');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/update.html.twig', [
            'user'         => $user,
            'form_user'    => $form->createView(),
            '_breadcrumbs' => $this->userBreadcrumbs->buildUpdate($user->getId()),
        ]);
    }

    #[IsGranted('user_update')]
    #[Route('/admin/user/{id}/update/password', name: 'admin_user_update_password')]
    public function updatePassword(UserPasswordHasherInterface $hasher, Request $request, UuidV4 $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        $data = new PlainPasswordData();
        $form = $this->createForm(RepeatedPasswordType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.user_password.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $plainPassword = $data->getPlainPassword();
            if ($plainPassword === null)
            {
                $user->setPassword(null);
                $this->addTransFlash('success', 'crud.action_performed.user.reset_password');
            }
            else
            {
                $password = $hasher->hashPassword($user, $plainPassword);
                $user->setPassword($password);
                $this->addTransFlash('success', 'crud.action_performed.user.update_password');
            }

            $this->userRepository->saveUser($user, true);

            return $this->redirectToRoute('admin_user_update', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/update_password.html.twig', [
            'user'          => $user,
            'form_password' => $form->createView(),
            '_breadcrumbs'  => $this->userBreadcrumbs->buildUpdatePassword($user->getId()),
        ]);
    }

    #[IsGranted('user_delete')]
    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.user_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->userRepository->removeUser($user, true);
            $this->addTransFlash('success', 'crud.action_performed.user.delete');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/delete.html.twig', [
            'user'         => $user,
            'form_delete'  => $form->createView(),
            '_breadcrumbs' => $this->userBreadcrumbs->buildDelete($user->getId()),
        ]);
    }

    private function findUserOrThrow404(UuidV4 $id): User
    {
        $user = $this->userRepository->findOneById($id);
        if ($user === null)
        {
            throw $this->createNotFoundException();
        }

        return $user;
    }
}