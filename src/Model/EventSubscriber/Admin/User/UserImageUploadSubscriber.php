<?php

namespace App\Model\EventSubscriber\Admin\User;

use App\Model\Event\Admin\User\ProfileUpdateEvent;
use App\Model\Event\Admin\User\UserCreateEvent;
use App\Model\Event\Admin\User\UserUpdateEvent;
use App\Model\Service\User\UserImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserImageUploadSubscriber
{
    private UserImageFilesystemInterface $userImageFilesystem;

    public function __construct(UserImageFilesystemInterface $userImageFilesystem)
    {
        $this->userImageFilesystem = $userImageFilesystem;
    }

    #[AsEventListener(event: UserCreateEvent::NAME, priority: 200)]
    #[AsEventListener(event: UserUpdateEvent::NAME, priority: 200)]
    #[AsEventListener(event: ProfileUpdateEvent::NAME, priority: 200)]
    public function onCreateOrUpdateUploadImage(UserCreateEvent|UserUpdateEvent|ProfileUpdateEvent $event): void
    {
        $user = $event->getUser();

        if ($event instanceof ProfileUpdateEvent)
        {
            $data = $event->getProfileData();
        }
        else
        {
            $data = $event->getUserData();
        }

        $image = $data->getImage();
        $removeImage = $data->removeImage();

        if ($image !== null && !$removeImage)
        {
            $this->userImageFilesystem->uploadImageFile($image, $user);
        }

        if ($removeImage)
        {
            $this->userImageFilesystem->removeImageFile($user);
        }
    }
}