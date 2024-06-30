<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\PlainPasswordData;
use App\Library\Data\User\RegistrationData;
use App\Model\Event\User\UserRegistration\UserRegistrationCompleteEvent;
use App\Model\Event\User\UserRegistration\UserRegistrationCreateEvent;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Service\Form\Type\User\RegistrationType;
use App\Service\Form\Type\User\RepeatedPasswordType;
use App\Service\Security\Hasher\UserRegistrationVerifierHasherInterface;
use App\Service\Security\Token\TokenSplitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    #[Route('', name: 'user_registration')]
    public function registration(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $registrationData = new RegistrationData();
        $form = $this->createForm(RegistrationType::class, $registrationData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserRegistrationCreateEvent($registrationData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'auth.registration_created', [
                'email' => $registrationData->getEmail(),
            ]);

            return $this->redirectToRoute('user_home');
        }

        return $this->render('user/auth/registration.html.twig', [
            'form_registration' => $form->createView(),
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/complete/{token}', name: 'user_registration_complete', requirements: ['token' => '\w+'])]
    public function registrationComplete(EventDispatcherInterface                $eventDispatcher,
                                         TokenSplitterInterface                  $tokenSplitter,
                                         UserRegistrationRepositoryInterface     $registrationRepository,
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
            $event = new UserRegistrationCompleteEvent($passwordData, $userRegistration);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'auth.registration_complete');

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/auth/registration_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            'breadcrumbs'         => $this->createBreadcrumbs([
                'token' => $token,
            ]),
        ]);
    }
}