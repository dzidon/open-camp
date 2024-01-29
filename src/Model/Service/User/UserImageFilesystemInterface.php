<?php

namespace App\Model\Service\User;

use App\Model\Entity\User;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Helper service for purchasable item image files.
 */
interface UserImageFilesystemInterface
{
    /**
     * Gets the path to the given purchasable item image.
     *
     * @param User $user
     * @return string
     */
    public function getImageFilePath(User $user): string;

    /**
     * Uploads the given file and attaches it to the given purchasable item.
     *
     * @param File $file
     * @param User $user
     * @return void
     */
    public function uploadImageFile(File $file, User $user): void;

    /**
     * Removes the image file of the given purchasable item.
     *
     * @param User $user
     * @return void
     */
    public function removeImageFile(User $user): void;
}