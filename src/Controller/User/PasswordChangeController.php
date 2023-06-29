<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DataTransfer\Data\User\PasswordChangeData;
use App\Form\Type\User\PasswordChangeType;
use App\Form\Type\User\RepeatedPasswordType;
use App\Mailer\UserPasswordChangeMailerInterface;
use App\Menu\Breadcrumbs\User\PasswordChangeBreadcrumbsInterface;
use App\Repository\UserPasswordChangeRepositoryInterface;
use App\Security\Hasher\UserPasswordChangeVerifierHasherInterface;
use App\Security\PasswordChange\UserPasswordChangeFactoryInterface;
use App\Security\PasswordChange\UserPasswordChangerInterface;
use App\Security\Token\TokenSplitterInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/password-change')]
#[IsGranted(new Expression('not is_authenticated()'), statusCode: 403)]
class PasswordChangeController extends AbstractController
{
    private PasswordChangeBreadcrumbsInterface $passwordChangeBreadcrumbs;

    public function __construct(PasswordChangeBreadcrumbsInterface $passwordChangeBreadcrumbs)
    {
        $this->passwordChangeBreadcrumbs = $passwordChangeBreadcrumbs;
    }

    #[Route('', name: 'user_password_change')]
    public function registration(UserPasswordChangeMailerInterface  $mailer,
                                 UserPasswordChangeFactoryInterface $passwordChangeFactory,
                                 Request                            $request): Response
    {
        $passwordChangeData = new PasswordChangeData();
        $form = $this->createForm(PasswordChangeType::class, $passwordChangeData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $result = $passwordChangeFactory->createUserPasswordChange($passwordChangeData->getEmail(), true);
            $userPasswordChange = $result->getUserPasswordChange();
            $user = $userPasswordChange->getUser();
            $email = ($user === null ? 'fake@email.com' : $user->getEmail());
            $mailer->sendEmail($email, $result->getToken(), $userPasswordChange->getExpireAt(), $result->isFake());
            $this->addTransFlash('success', 'auth.password_change_created');

            return $this->redirectToRoute('user_home');
        }

        return $this->render('user/auth/password_change.html.twig', [
            'form_password_change' => $form->createView(),
            '_breadcrumbs' => $this->passwordChangeBreadcrumbs->buildPasswordChange(),
        ]);
    }

    #[Route('/complete/{token}', name: 'user_password_change_complete', requirements: ['token' => '\w+'])]
    public function registrationComplete(TokenSplitterInterface                    $tokenSplitter,
                                         UserPasswordChangeRepositoryInterface     $passwordChangeRepository,
                                         UserPasswordChangerInterface              $userPasswordChanger,
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

        $passwordData = new \App\Form\DataTransfer\Data\User\PlainPasswordData();
        $form = $this->createForm(RepeatedPasswordType::class, $passwordData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change_complete.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userPasswordChanger->completeUserPasswordChange($userPasswordChange, $passwordData->getPlainPassword(), true);
            $this->addTransFlash('success', 'auth.password_changed');

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/auth/password_change_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            '_breadcrumbs' => $this->passwordChangeBreadcrumbs->buildPasswordChangeComplete($token),
        ]);
    }
}