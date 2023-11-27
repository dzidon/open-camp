<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\PlainPasswordData;
use App\Library\Data\Admin\UserData;
use App\Library\Data\Admin\UserSearchData;
use App\Model\Entity\User;
use App\Model\Event\Admin\User\UserCreateEvent;
use App\Model\Event\Admin\User\UserDeleteEvent;
use App\Model\Event\Admin\User\UserUpdateEvent;
use App\Model\Event\Admin\User\UserUpdatePasswordEvent;
use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\RepeatedPasswordType;
use App\Service\Form\Type\Admin\UserSearchType;
use App\Service\Form\Type\Admin\UserType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\UserBreadcrumbsInterface;
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
        $form = $formFactory->createNamed('', UserSearchType::class, $searchData, [
            'choices_roles' => $roleRepository->findAll(),
        ]);
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
            'breadcrumbs'       => $this->userBreadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('user_create')]
    #[Route('/admin/user/create', name: 'admin_user_create')]
    public function create(EventDispatcherInterface $eventDispatcher,
                           RoleRepositoryInterface  $roleRepository,
                           Request                  $request): Response
    {
        $userData = new UserData($this->getParameter('app.eu_business_data'));

        $form = $this->createForm(UserType::class, $userData, [
            'choices_roles' => $roleRepository->findAll(),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.user.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserCreateEvent($userData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.user.create');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/update.html.twig', [
            'form_user'   => $form->createView(),
            'breadcrumbs' => $this->userBreadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('user_read')]
    #[Route('/admin/user/{id}/read', name: 'admin_user_read')]
    public function read(UuidV4 $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        return $this->render('admin/user/read.html.twig', [
            'user'        => $user,
            'breadcrumbs' => $this->userBreadcrumbs->buildRead($user),
        ]);
    }

    #[IsGranted(new Expression('is_granted("user_update") or is_granted("user_update_role")'))]
    #[Route('/admin/user/{id}/update', name: 'admin_user_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           RoleRepositoryInterface       $roleRepository,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        $userData = new UserData($this->getParameter('app.eu_business_data'), $user);
        $dataTransfer->fillData($userData, $user);

        $form = $this->createForm(UserType::class, $userData, [
            'choices_roles' => $roleRepository->findAll(),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.user.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserUpdateEvent($userData, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.user.update');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/update.html.twig', [
            'user'        => $user,
            'form_user'   => $form->createView(),
            'breadcrumbs' => $this->userBreadcrumbs->buildUpdate($user),
        ]);
    }

    #[IsGranted('user_update')]
    #[Route('/admin/user/{id}/update/password', name: 'admin_user_update_password')]
    public function updatePassword(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $user = $this->findUserOrThrow404($id);

        $data = new PlainPasswordData();
        $form = $this->createForm(RepeatedPasswordType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.user_password.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserUpdatePasswordEvent($data, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $flashMessage = $user->getPassword() === null ? 'crud.action_performed.user.reset_password' : 'crud.action_performed.user.update_password';
            $this->addTransFlash('success', $flashMessage);

            return $this->redirectToRoute('admin_user_update', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/update_password.html.twig', [
            'user'          => $user,
            'form_password' => $form->createView(),
            'breadcrumbs'   => $this->userBreadcrumbs->buildUpdatePassword($user),
        ]);
    }

    #[IsGranted('user_delete')]
    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
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
            $event = new UserDeleteEvent($user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.user.delete');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/delete.html.twig', [
            'user'        => $user,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->userBreadcrumbs->buildDelete($user),
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