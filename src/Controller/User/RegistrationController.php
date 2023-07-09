<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DataTransfer\Data\User\PlainPasswordData;
use App\Form\DataTransfer\Data\User\RegistrationData;
use App\Form\Type\User\RegistrationType;
use App\Form\Type\User\RepeatedPasswordType;
use App\Mailer\UserRegistrationMailerInterface;
use App\Menu\Breadcrumbs\User\RegistrationBreadcrumbsInterface;
use App\Model\Module\Security\UserRegistration\UserRegistererInterface;
use App\Model\Module\Security\UserRegistration\UserRegistrationFactoryInterface;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Security\Hasher\UserRegistrationVerifierHasherInterface;
use App\Security\Token\TokenSplitterInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('not is_authenticated()'), statusCode: 403)]
#[Route('/registration')]
class RegistrationController extends AbstractController
{
    private RegistrationBreadcrumbsInterface $registrationBreadcrumbs;

    public function __construct(RegistrationBreadcrumbsInterface $registrationBreadcrumbs)
    {
        $this->registrationBreadcrumbs = $registrationBreadcrumbs;
    }

    #[Route('', name: 'user_registration')]
    public function registration(UserRegistrationMailerInterface  $mailer,
                                 UserRegistrationFactoryInterface $registrationFactory,
                                 Request                          $request): Response
    {
        $registrationData = new RegistrationData();
        $form = $this->createForm(RegistrationType::class, $registrationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.registration.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $result = $registrationFactory->createUserRegistration($registrationData->getEmail(), true);
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
    public function registrationComplete(TokenSplitterInterface                  $tokenSplitter,
                                         UserRegistrationRepositoryInterface     $registrationRepository,
                                         UserRegistererInterface                 $userRegisterer,
                                         UserRegistrationVerifierHasherInterface $hasher,
                                         Request                                 $request,
                                         string                                  $token): Response
    {
        $tokenSplit = $tokenSplitter->splitToken($token);
        $userRegistration = $registrationRepository->findOneBySelector($tokenSplit->getSelector(), true);

        if ($userRegistration === null || !$hasher->isVerifierValid($userRegistration, $tokenSplit->getPlainVerifier()))
        {
            throw $this->createNotFoundException();
        }

        $passwordData = new PlainPasswordData();
        $form = $this->createForm(RepeatedPasswordType::class, $passwordData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.registration_password.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userRegisterer->completeUserRegistration($userRegistration, $passwordData->getPlainPassword(), true);
            $this->addTransFlash('success', 'auth.registration_complete');

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/auth/registration_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            '_breadcrumbs' => $this->registrationBreadcrumbs->buildRegistrationComplete($token),
        ]);
    }
}