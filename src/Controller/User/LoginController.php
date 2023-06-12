<?php

namespace App\Controller\User;

use App\Form\DTO\User\LoginDTO;
use App\Form\Type\User\LoginType;
use App\Menu\Breadcrumbs\User\LoginBreadcrumbsInterface;
use App\Security\SocialLoginRedirectResponseFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/login')]
#[IsGranted(new Expression('not is_granted("IS_AUTHENTICATED_FULLY")'), statusCode: 403)]
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
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error)
        {
            $messageKey = $error->getMessageKey();
            $messageData = $error->getMessageData();
            $errorMessage = $this->translator->trans($messageKey, $messageData, 'security');
            $this->addFlash('failure', $errorMessage);
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