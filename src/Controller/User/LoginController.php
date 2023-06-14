<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DTO\User\LoginDTO;
use App\Form\Type\User\LoginType;
use App\Menu\Breadcrumbs\User\LoginBreadcrumbsInterface;
use App\Security\SocialLoginRedirectResponseFactoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/login')]
#[IsGranted(new Expression('not is_granted("IS_AUTHENTICATED_FULLY")'), statusCode: 403)]
class LoginController extends AbstractController
{
    private LoginBreadcrumbsInterface $breadcrumbs;

    public function __construct(LoginBreadcrumbsInterface $breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

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

        $dto = new LoginDTO();
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
        return $responseFactory->createRedirectResponse($service);
    }
}