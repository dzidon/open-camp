<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DTO\User\PlainPasswordDTO;
use App\Form\DTO\User\RegistrationDTO;
use App\Form\Type\User\RepeatedPasswordType;
use App\Form\Type\User\RegistrationType;
use App\Mailer\UserRegistrationMailerInterface;
use App\Menu\Breadcrumbs\User\RegistrationBreadcrumbsInterface;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Security\TokenSplitterInterface;
use App\Security\UserRegistererInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/registration')]
#[IsGranted(new Expression('not is_authenticated()'), statusCode: 403)]
class RegistrationController extends AbstractController
{
    private UserRegistererInterface $userRegisterer;
    private RegistrationBreadcrumbsInterface $registrationBreadcrumbs;

    public function __construct(UserRegistererInterface $userRegisterer, RegistrationBreadcrumbsInterface $registrationBreadcrumbs)
    {
        $this->userRegisterer = $userRegisterer;
        $this->registrationBreadcrumbs = $registrationBreadcrumbs;
    }

    #[Route('', name: 'user_registration')]
    public function registration(UserRegistrationMailerInterface $mailer, Request $request): Response
    {
        $registrationDTO = new RegistrationDTO();
        $form = $this->createForm(RegistrationType::class, $registrationDTO);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.registration.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $result = $this->userRegisterer->createUserRegistration((string) $registrationDTO->email, true);
            $userRegistration = $result->getUserRegistration();
            $mailer->sendEmail($userRegistration->getEmail(), $result->getToken(), $userRegistration->getExpireAt(), $result->isFake());
            $this->addTransFlash('success', 'auth.registration_created');

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
        $userRegistration = $registrationRepository->findOneBySelector($tokenSplit->getSelector(), true);

        if ($userRegistration === null || !$this->userRegisterer->verify($userRegistration, $tokenSplit->getPlainVerifier()))
        {
            throw $this->createNotFoundException();
        }

        $passwordDTO = new PlainPasswordDTO();
        $form = $this->createForm(RepeatedPasswordType::class, $passwordDTO);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.registration_password.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->userRegisterer->completeUserRegistration($userRegistration, (string) $passwordDTO->plainPassword, true);
            $this->addTransFlash('success', 'auth.registration_complete');

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/auth/registration_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            '_breadcrumbs' => $this->registrationBreadcrumbs->buildRegistrationComplete($token),
        ]);
    }
}