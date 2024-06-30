<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\LoginData;
use App\Service\Form\Type\User\LoginType;
use App\Service\Security\Authentication\SocialLoginRedirectResponseFactoryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

#[IsGranted(new Expression('not is_granted("IS_AUTHENTICATED_FULLY")'), statusCode: 403)]
#[Route('/login')]
class LoginController extends AbstractController
{
    #[Route('', name: 'user_login')]
    public function login(Request              $request,
                          FormFactoryInterface $formFactory,
                          AuthenticationUtils  $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error)
        {
            $this->addTransFlash('failure', $error->getMessageKey(), $error->getMessageData(), 'security');
        }

        $loginData = new LoginData();
        $loginData->setEmail($authenticationUtils->getLastUsername());
        $form = $formFactory->createNamed('', LoginType::class, $loginData);
        $request->getSession()->remove(SecurityRequestAttributes::LAST_USERNAME);

        return $this->render('user/auth/login.html.twig', [
            'form_login'  => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/{service}', name: 'user_login_oauth')]
    public function loginSocial(SocialLoginRedirectResponseFactoryInterface $responseFactory, string $service): RedirectResponse
    {
        $socialLoginServicesData = $this->getParameter('app.social_login_services');
        $validServiceIds = array_keys($socialLoginServicesData);

        if (!in_array($service, $validServiceIds))
        {
            throw $this->createNotFoundException();
        }

        return $responseFactory->createRedirectResponse($service);
    }
}