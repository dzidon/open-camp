<?php

namespace App\Controller\User;

use App\Form\DTO\User\RegistrationPasswordDTO;
use App\Form\DTO\User\RegistrationDTO;
use App\Form\Type\User\RegistrationPasswordType;
use App\Form\Type\User\RegistrationType;
use App\Menu\Breadcrumbs\User\RegistrationBreadcrumbsInterface;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Security\TokenSplitterInterface;
use App\Security\UserRegistererInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/registration', condition: '')]
#[IsGranted(new Expression('not is_authenticated()'), statusCode: 403)]
class RegistrationController extends AbstractController
{
    private TranslatorInterface $translator;
    private UserRegistererInterface $userRegisterer;
    private RegistrationBreadcrumbsInterface $registrationBreadcrumbs;

    public function __construct(TranslatorInterface $translator,
                                UserRegistererInterface $userRegisterer,
                                RegistrationBreadcrumbsInterface $registrationBreadcrumbs)
    {
        $this->translator = $translator;
        $this->userRegisterer = $userRegisterer;
        $this->registrationBreadcrumbs = $registrationBreadcrumbs;
    }

    #[Route('', name: 'user_registration')]
    public function registration(Request $request): Response
    {
        $registrationDTO = new RegistrationDTO();
        $form = $this->createForm(RegistrationType::class, $registrationDTO);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.registration.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->userRegisterer->createUserRegistration((string) $registrationDTO->email);
            $successMessage = $this->translator->trans('auth.registration_created');
            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('user_home');
        }

        return $this->render('user/auth/registration.html.twig', [
            'form_registration' => $form->createView(),
            '_breadcrumbs' => $this->registrationBreadcrumbs->buildRegistration(),
        ]);
    }

    #[Route('/complete/{token}', name: 'user_registration_complete', requirements: ['token' => '\w+'])]
    public function registrationComplete(TokenSplitterInterface $tokenSplitter,
                                         UserRegistrationRepositoryInterface $registrationRepository,
                                         Request $request,
                                         string $token): Response
    {
        $tokenSplit = $tokenSplitter->splitToken($token);
        $selector = $tokenSplit->getSelector();
        $plainVerifier = $tokenSplit->getPlainVerifier();
        $userRegistration = $registrationRepository->findOneBySelector($selector, true);

        if ($userRegistration === null || !$this->userRegisterer->verify($userRegistration, $plainVerifier))
        {
            throw $this->createNotFoundException();
        }

        $passwordDTO = new RegistrationPasswordDTO();
        $form = $this->createForm(RegistrationPasswordType::class, $passwordDTO);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.registration_password.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->userRegisterer->completeUserRegistration($userRegistration, (string) $passwordDTO->plainPassword);
            $successMessage = $this->translator->trans('auth.registration_complete');
            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/auth/registration_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            '_breadcrumbs' => $this->registrationBreadcrumbs->buildRegistrationComplete($token),
        ]);
    }
}