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
        $imageFileName = $user->getImageFileName();

        if ($imageFileName === null || !$this->userImageStorage->has($imageFileName))
        {
            return null;
        }

        return $this->userImageStorage->lastModified($imageFileName);
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
        $imageFileName = $user->getImageFileName();

        if ($imageFileName === null || !$this->userImageStorage->has($imageFileName))
        {
            return $noImageUrl;
        }

        return $this->userImagePublicPathPrefix . $this->userImageDirectory . '/' . $imageFileName;
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
        $imageFileName = $user->getImageFileName();

        if ($imageFileName === null)
        {
            return;
        }

        $this->userImageStorage->delete($imageFileName);
        $user->setImageExtension(null);
    }

    private function getNoImageUrl(): string
    {
        return $this->userImagePublicPathPrefix . $this->noImagePath;
    }
}