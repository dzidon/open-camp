<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DTO\User\PasswordChangeDTO;
use App\Form\DTO\User\PlainPasswordDTO;
use App\Form\Type\User\PasswordChangeType;
use App\Form\Type\User\RepeatedPasswordType;
use App\Mailer\UserPasswordChangeMailerInterface;
use App\Menu\Breadcrumbs\User\PasswordChangeBreadcrumbsInterface;
use App\Repository\UserPasswordChangeRepositoryInterface;
use App\Security\TokenSplitterInterface;
use App\Security\UserPasswordChangerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/password-change')]
#[IsGranted(new Expression('not is_authenticated()'), statusCode: 403)]
class PasswordChangeController extends AbstractController
{
    private UserPasswordChangerInterface $userPasswordChanger;
    private PasswordChangeBreadcrumbsInterface $passwordChangeBreadcrumbs;

    public function __construct(UserPasswordChangerInterface $userPasswordChanger, PasswordChangeBreadcrumbsInterface $passwordChangeBreadcrumbs)
    {
        $this->userPasswordChanger = $userPasswordChanger;
        $this->passwordChangeBreadcrumbs = $passwordChangeBreadcrumbs;
    }

    #[Route('', name: 'user_password_change')]
    public function registration(UserPasswordChangeMailerInterface $mailer, Request $request): Response
    {
        $passwordChangeDTO = new PasswordChangeDTO();
        $form = $this->createForm(PasswordChangeType::class, $passwordChangeDTO);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $result = $this->userPasswordChanger->createUserPasswordChange((string) $passwordChangeDTO->email, true);
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
    public function registrationComplete(TokenSplitterInterface $tokenSplitter,
                                         UserPasswordChangeRepositoryInterface $passwordChangeRepository,
                                         Request $request,
                                         string $token): Response
    {
        $tokenSplit = $tokenSplitter->splitToken($token);
        $userPasswordChange = $passwordChangeRepository->findOneBySelector($tokenSplit->getSelector(), true);

        if ($userPasswordChange === null || !$this->userPasswordChanger->verify($userPasswordChange, $tokenSplit->getPlainVerifier()))
        {
            throw $this->createNotFoundException();
        }

        $passwordDTO = new PlainPasswordDTO();
        $form = $this->createForm(RepeatedPasswordType::class, $passwordDTO);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change_complete.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->userPasswordChanger->completeUserPasswordChange($userPasswordChange, (string) $passwordDTO->plainPassword, true);
            $this->addTransFlash('success', 'auth.password_changed');

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/auth/password_change_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            '_breadcrumbs' => $this->passwordChangeBreadcrumbs->buildPasswordChangeComplete($token),
        ]);
    }
}