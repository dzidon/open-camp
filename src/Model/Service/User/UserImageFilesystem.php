<?php

namespace App\Model\Service\User;

use App\Model\Entity\User;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class UserImageFilesystem implements UserImageFilesystemInterface
{
    private string $userImageDirectory;
    private string $noImagePath;
    private string $kernelProjectDirectory;

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem,
                                string     $userImageDirectory,
                                string     $noImagePath,
                                string     $kernelProjectDirectory)
    {
        $this->filesystem = $filesystem;

        $this->userImageDirectory = $userImageDirectory;
        $this->kernelProjectDirectory = $kernelProjectDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getImageFilePath(User $user): string
    {
        if ($user->getImageExtension() === null)
        {
            return $this->noImagePath;
        }

        $id = $user->getId();

        return $this->userImageDirectory . '/' . $id->toRfc4122() . '.' . $user->getImageExtension();
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
        $file->move($this->userImageDirectory, $newFileName);
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

        $filePath = $this->kernelProjectDirectory . '/public/' . $this->getImageFilePath($user);
        $this->filesystem->remove($filePath);
        $user->setImageExtension(null);
    }
}