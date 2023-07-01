<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Form\DataTransfer\Data\User\ProfilePasswordChangeData;
use App\Form\Type\Common\HiddenTrueType;
use App\Form\Type\User\ProfilePasswordChangeType;
use App\Mailer\UserPasswordChangeMailerInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\PasswordChange\UserPasswordChangeFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/password-change', name: 'user_profile_password_change')]
    public function passwordChange(UserPasswordHasherInterface $hasher,
                                   UserRepositoryInterface     $userRepository,
                                   Request                     $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getPassword() === null)
        {
            return $this->redirectToRoute('user_profile_password_change_create');
        }

        $passwordChangeData = new ProfilePasswordChangeData();
        $form = $this->createForm(ProfilePasswordChangeType::class, $passwordChangeData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.profile_password_change.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (!$hasher->isPasswordValid($user, $passwordChangeData->getCurrentPassword()))
            {
                $this->addTransFlash('failure', 'auth.profile_password_change_wrong');

                return $this->redirectToRoute('user_profile_password_change');
            }

            $newPasswordChangeData = $passwordChangeData->getNewPasswordData();
            $newPassword = $hasher->hashPassword($user, $newPasswordChangeData->getPlainPassword());
            $user->setPassword($newPassword);
            $userRepository->saveUser($user, true);
            $this->addTransFlash('success', 'auth.profile_password_changed');

            return $this->redirectToRoute('user_profile_password_change');
        }

        return $this->render('user/profile/password_change.html.twig', [
            'form_password_change' => $form,
        ]);
    }

    #[Route('/password-change-create', name: 'user_profile_password_change_create')]
    public function passwordSet(UserPasswordChangeFactoryInterface $passwordChangeFactory,
                                UserPasswordChangeMailerInterface  $mailer,
                                Request                            $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getPassword() !== null)
        {
            return $this->redirectToRoute('user_profile_password_change');
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.profile_password_change_create.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $result = $passwordChangeFactory->createUserPasswordChange($user->getEmail(), true);
            $passwordChange = $result->getUserPasswordChange();
            $mailer->sendEmail($user->getEmail(), $result->getToken(), $passwordChange->getExpireAt(), $result->isFake());
            $this->addTransFlash('success', 'auth.profile_password_change_created', ['email' => $user->getEmail()]);

            return $this->redirectToRoute('user_profile_password_change_create');
        }

        return $this->render('user/profile/password_change_create.html.twig', [
            'form_password_change_create' => $form,
        ]);
    }
}