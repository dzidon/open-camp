<?php

namespace App\Model\Service\User;

use App\Model\Entity\User;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class UserImageFilesystem implements UserImageFilesystemInterface
{
    private FilesystemOperator $userImageStorage;

    private string $userImagePublicPathPrefix;

    private string $userImageDirectory;

    private string $noImagePath;

    public function __construct(FilesystemOperator $userImageStorage,
                                string             $userImagePublicPathPrefix,
                                string             $userImageDirectory,
                                string             $noImagePath)
    {
        $this->userImageStorage = $userImageStorage;

        $this->userImagePublicPathPrefix = $userImagePublicPathPrefix;
        $this->userImageDirectory = $userImageDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getImageLastModified(User $user): ?int
    {
        if ($user->getImageExtension() === null)
        {
            return null;
        }

        $fileName = $this->getUserImageName($user);

        if (!$this->userImageStorage->has($fileName))
        {
            return null;
        }

        return $this->userImageStorage->lastModified($fileName);
    }

    /**
     * @inheritDoc
     */
    public function isUrlPlaceholder(string $publicUrl): bool
    {
        return explode('?', $publicUrl)[0] === $this->getNoImageUrl();
    }

    /**
     * @inheritDoc
     */
    public function getImagePublicUrl(User $user): string
    {
        $noImageUrl = $this->getNoImageUrl();

        if ($user->getImageExtension() === null)
        {
            return $noImageUrl;
        }

        $fileName = $this->getUserImageName($user);

        if (!$this->userImageStorage->has($fileName))
        {
            return $noImageUrl;
        }

        $fileName = $this->getUserImageName($user);

        return $this->userImagePublicPathPrefix . $this->userImageDirectory . '/' . $fileName;
    }

    /**
     * @inheritDoc
     */
    public function uploadImageFile(File $file, User $user): void
    {
        $this->removeImageFile($user);

        $extension = $file->guessExtension();
        $user->setImageExtension($extension);
        $idString = $user
            ->getId()
            ->toRfc4122()
        ;

        $newFileName = $idString . '.' . $extension;
        $contents = $file->getContent();
        $this->userImageStorage->write($newFileName, $contents);
    }

    /**
     * @inheritDoc
     */
    public function removeImageFile(User $user): void
    {
        if ($user->getImageExtension() === null)
        {
            return;
        }

        $fileName = $this->getUserImageName($user);
        $this->userImageStorage->delete($fileName);
        $user->setImageExtension(null);
    }

    private function getUserImageName(User $user): string
    {
        $userId = $user->getId();

        return $userId->toRfc4122() . '.' . $user->getImageExtension();
    }

    private function getNoImageUrl(): string
    {
        return $this->userImagePublicPathPrefix . $this->noImagePath;
    }
}