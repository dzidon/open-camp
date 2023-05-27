<?php

namespace App\Controller\User;

use App\Form\DTO\UserLoginDTO;
use App\Form\Type\User\LoginType;
use App\Menu\Breadcrumbs\User\LoginBreadcrumbsInterface;
use App\Security\SocialLoginRedirectResponseFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/login')]
class LoginController extends AbstractController
{
    private LoginBreadcrumbsInterface $breadcrumbs;
    private TranslatorInterface $translator;

    public function __construct(LoginBreadcrumbsInterface $breadcrumbs, TranslatorInterface $translator)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->translator = $translator;
    }

    #[Route('', name: 'user_login')]
    public function login(Request              $request,
                          FormFactoryInterface $formFactory,
                          AuthenticationUtils  $authenticationUtils): Response
    {
        if (($redirectHome = $this->redirectHomeIfAuthenticatedFully()) !== null)
        {
            return $redirectHome;
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error)
        {
            $messageKey = $error->getMessageKey();
            $messageData = $error->getMessageData();
            $errorMessage = $this->translator->trans($messageKey, $messageData, 'security');
            $this->addFlash('failure', $errorMessage);
        }

        $dto = new UserLoginDTO();
        $dto->email = $authenticationUtils->getLastUsername();
        $form = $formFactory->createNamed('', LoginType::class, $dto);

        $form->add('submit', SubmitType::class, ['label' => 'form.user.login.button']);
        $request->getSession()->remove(Security::LAST_USERNAME);

        return $this->render('user/auth/login.html.twig', [
            'form_login' => $form->createView(),
            '_breadcrumbs' => $this->breadcrumbs->buildLogin(),
        ]);
    }

    #[Route('/{service<%app.social_login_services%>}', name: 'user_login_oauth')]
    public function loginSocial(SocialLoginRedirectResponseFactoryInterface $responseFactory, $service): RedirectResponse
    {
        if (($redirectHome = $this->redirectHomeIfAuthenticatedFully()) !== null)
        {
            return $redirectHome;
        }

        return $responseFactory->createRedirectResponse($service);
    }

    /**
     * Adds an error flash message and creates a home redirect response if the user is authenticated fully.
     *
     * @return RedirectResponse|null
     */
    private function redirectHomeIfAuthenticatedFully(): ?RedirectResponse
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            $errorMessage = $this->translator->trans('auth.already_logged_in');
            $this->addFlash('failure', $errorMessage);
            return $this->redirectToRoute('user_home');
        }

        return null;
    }
}