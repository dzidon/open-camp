<?php

namespace App\Controller\User;

use App\Form\DTO\UserLoginDTO;
use App\Form\Type\User\LoginType;
use App\Menu\Breadcrumbs\User\LoginBreadcrumbsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class LoginController extends AbstractController
{
    private LoginBreadcrumbsInterface $breadcrumbs;

    public function __construct(LoginBreadcrumbsInterface $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/login', name: 'user_login')]
    public function login(Request              $request,
                          FormFactoryInterface $formFactory,
                          AuthenticationUtils  $authenticationUtils,
                          TranslatorInterface  $translator): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $errorMessage = $translator->trans('auth.already_logged_in');
            $this->addFlash('failure', $errorMessage);
            return $this->redirectToRoute('user_home');
        }

        $this->breadcrumbs->initializeLogin();

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error)
        {
            $errorMessage = $translator->trans($error->getMessageKey(), $error->getMessageData(), 'security');
            $this->addFlash('failure', $errorMessage);
        }

        $dto = new UserLoginDTO();
        $dto->email = $authenticationUtils->getLastUsername();
        $form = $formFactory->createNamed('', LoginType::class, $dto);

        $form->add('submit', SubmitType::class, ['label' => 'form.user.login.button']);
        $request->getSession()->remove(Security::LAST_USERNAME);

        return $this->render('user/auth/login.html.twig', [
            'form_login' => $form->createView(),
        ]);
    }
}