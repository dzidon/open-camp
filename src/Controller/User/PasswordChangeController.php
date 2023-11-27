<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\PasswordChangeData;
use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\User;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompleteEvent;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreateEvent;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Service\Form\Type\User\PasswordChangeType;
use App\Service\Form\Type\User\RepeatedPasswordType;
use App\Service\Menu\Breadcrumbs\User\PasswordChangeBreadcrumbsInterface;
use App\Service\Security\Hasher\UserPasswordChangeVerifierHasherInterface;
use App\Service\Security\Token\TokenSplitterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/password-change')]
class PasswordChangeController extends AbstractController
{
    private PasswordChangeBreadcrumbsInterface $passwordChangeBreadcrumbs;

    public function __construct(PasswordChangeBreadcrumbsInterface $passwordChangeBreadcrumbs)
    {
        $this->passwordChangeBreadcrumbs = $passwordChangeBreadcrumbs;
    }

    #[IsGranted(new Expression('not is_authenticated()'), statusCode: 403)]
    #[Route('', name: 'user_password_change')]
    public function passwordChange(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $passwordChangeData = new PasswordChangeData();
        $form = $this->createForm(PasswordChangeType::class, $passwordChangeData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserPasswordChangeCreateEvent($passwordChangeData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'auth.password_change_created', [
                'email' => $passwordChangeData->getEmail(),
            ]);

            return $this->redirectToRoute('user_home');
        }

        return $this->render('user/auth/password_change.html.twig', [
            'form_password_change' => $form->createView(),
            'breadcrumbs'          => $this->passwordChangeBreadcrumbs->buildPasswordChange(),
        ]);
    }

    #[Route('/complete/{token}', name: 'user_password_change_complete', requirements: ['token' => '\w+'])]
    public function passwordChangeComplete(EventDispatcherInterface                  $eventDispatcher,
                                           TokenSplitterInterface                    $tokenSplitter,
                                           UserPasswordChangeRepositoryInterface     $passwordChangeRepository,
                                           UserPasswordChangeVerifierHasherInterface $hasher,
                                           Request                                   $request,
                                           string                                    $token): Response
    {
        $tokenSplit = $tokenSplitter->splitToken($token);
        $userPasswordChange = $passwordChangeRepository->findOneBySelector($tokenSplit->getSelector(), true);

        if ($userPasswordChange === null || !$hasher->isVerifierValid($userPasswordChange, $tokenSplit->getPlainVerifier()))
        {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $isAuthenticated = $user !== null;

        $passwordData = new PlainPasswordData();
        $form = $this->createForm(RepeatedPasswordType::class, $passwordData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change_complete.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserPasswordChangeCompleteEvent($passwordData, $userPasswordChange);
            $eventDispatcher->dispatch($event, $event::NAME);

            if ($isAuthenticated)
            {
                $this->addTransFlash('success', 'auth.password_changed_authenticated');
                return $this->redirectToRoute('user_home');
            }
            else
            {
                $this->addTransFlash('success', 'auth.password_changed_unauthenticated');
                return $this->redirectToRoute('user_login');
            }
        }

        return $this->render('user/auth/password_change_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            'breadcrumbs'         => $this->passwordChangeBreadcrumbs->buildPasswordChangeComplete($token, $isAuthenticated),
        ]);
    }
}