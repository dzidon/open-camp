<?php

namespace App\Model\EventSubscriber\User\UserPasswordChange;

use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCompleteEvent;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Model\Service\UserPasswordChange\UserPasswordChangerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserPasswordChangeCompleteSubscriber
{
    private UserPasswordChangerInterface $userPasswordChanger;

    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;

    private UserRepositoryInterface $userRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordChangerInterface          $userPasswordChanger,
                                UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserRepositoryInterface               $userRepository,
                                EntityManagerInterface                $entityManager)
    {
        $this->userPasswordChanger = $userPasswordChanger;
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: UserPasswordChangeCompleteEvent::NAME, priority: 200)]
    public function onCompleteUpdate(UserPasswordChangeCompleteEvent $event): void
    {
        $userPasswordChange = $event->getUserPasswordChange();
        $passwordData = $event->getPlainPasswordData();
        $plainPassword = $passwordData->getPlainPassword();
        $result = $this->userPasswordChanger->completeUserPasswordChange($userPasswordChange, $plainPassword);
        $event->setUserPasswordChangeCompletionResult($result);
    }

    #[AsEventListener(event: UserPasswordChangeCompleteEvent::NAME, priority: 100)]
    public function onCompleteSaveEntities(UserPasswordChangeCompleteEvent $event): void
    {
        $result = $event->getUserPasswordChangeCompletionResult();
        $userPasswordChange = $result->getUsedUserPasswordChange();
        $disabledUserPasswordChanges = $result->getDisabledUserPasswordChanges();
        $isFlush = $event->isFlush();
        $canFlush = false;

        if ($userPasswordChange !== null)
        {
            $user = $userPasswordChange->getUser();

            if ($user !== null)
            {
                $this->userRepository->saveUser($user, false);
            }

            $this->userPasswordChangeRepository->saveUserPasswordChange($userPasswordChange, false);
            $canFlush = true;
        }

        foreach ($disabledUserPasswordChanges as $disabledUserPasswordChange)
        {
            $this->userPasswordChangeRepository->saveUserPasswordChange($disabledUserPasswordChange, false);
            $canFlush = true;
        }

        if ($isFlush && $canFlush)
        {
            $this->entityManager->flush();
        }
    }
}