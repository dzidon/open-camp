<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\PasswordChangeData;
use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\User;
use App\Model\Module\Security\UserPasswordChange\UserPasswordChangeFactoryInterface;
use App\Model\Module\Security\UserPasswordChange\UserPasswordChangerInterface;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Service\Form\Type\User\PasswordChangeType;
use App\Service\Form\Type\User\RepeatedPasswordType;
use App\Service\Mailer\UserPasswordChangeMailerInterface;
use App\Service\Menu\Breadcrumbs\User\PasswordChangeBreadcrumbsInterface;
use App\Service\Security\Hasher\UserPasswordChangeVerifierHasherInterface;
use App\Service\Security\Token\TokenSplitterInterface;
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
            'breadcrumbs'          => $this->passwordChangeBreadcrumbs->buildPasswordChange(),
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

        $passwordData = new PlainPasswordData();
        $form = $this->createForm(RepeatedPasswordType::class, $passwordData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.password_change_complete.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userPasswordChanger->completeUserPasswordChange($userPasswordChange, $passwordData->getPlainPassword(), true);

            /** @var User $user */
            $user = $this->getUser();
            if ($user === null)
            {
                $this->addTransFlash('success', 'auth.password_changed_unauthenticated');
                return $this->redirectToRoute('user_login');
            }
            else
            {
                $this->addTransFlash('success', 'auth.password_changed_authenticated');
                return $this->redirectToRoute('user_home');
            }
        }

        return $this->render('user/auth/password_change_complete.html.twig', [
            'form_plain_password' => $form->createView(),
            'breadcrumbs'         => $this->passwordChangeBreadcrumbs->buildPasswordChangeComplete($token),
        ]);
    }
}