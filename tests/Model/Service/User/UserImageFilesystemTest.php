<?php

namespace App\Tests\Model\Service\User;

use App\Model\Entity\User;
use App\Model\Service\User\UserImageFilesystem;
use App\Tests\Library\Http\File\FileMock;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserImageFilesystemTest extends KernelTestCase
{
    private FilesystemOperator $storage;

    private UserImageFilesystem $userImageFilesystem;

    private User $user;

    private FileMock $imageFile;

    private string $userIdString;

    public function testGetImageLastModified(): void
    {
        $this->userImageFilesystem->uploadImageFile($this->imageFile, $this->user);
        $this->assertSame(time(), $this->userImageFilesystem->getImageLastModified($this->user));
    }

    public function testGetImageLastModifiedForNonexistentFile(): void
    {
        $this->assertNull($this->userImageFilesystem->getImageLastModified($this->user));
    }

    public function testIsUrlPlaceholder(): void
    {
        $this->assertTrue($this->userImageFilesystem->isUrlPlaceholder('/files/static/user_placeholder.png'));
        $this->assertTrue($this->userImageFilesystem->isUrlPlaceholder('/files/static/user_placeholder.png?foo=bar&xyz=123'));
        $this->assertFalse($this->userImageFilesystem->isUrlPlaceholder('/a/b/c'));
    }

    public function testGetImagePublicUrl(): void
    {
        $this->userImageFilesystem->uploadImageFile($this->imageFile, $this->user);
        $actualUrl = $this->userImageFilesystem->getImagePublicUrl($this->user);
        $expectedUrl = '/files/dynamic/user/' . $this->userIdString . '.png';

        $this->assertSame($expectedUrl, $actualUrl);
    }

    public function testGetImagePublicUrlWithNonexistentFile(): void
    {
        $this->user->setImageExtension('jpg');
        $actualUrl = $this->userImageFilesystem->getImagePublicUrl($this->user);

        $this->assertSame('/files/static/user_placeholder.png', $actualUrl);
    }

    public function testGetImagePublicUrlWithNullCampImage(): void
    {
        $this->user->setImageExtension(null);
        $actualUrl = $this->userImageFilesystem->getImagePublicUrl($this->user);

        $this->assertSame('/files/static/user_placeholder.png', $actualUrl);
    }

    public function testUploadAndRemoveFile(): void
    {
        $fileName = $this->userIdString . '.png';

        $this->userImageFilesystem->uploadImageFile($this->imageFile, $this->user);
        $this->assertTrue($this->storage->has($fileName));
        $this->assertSame('png', $this->user->getImageExtension());

        $this->userImageFilesystem->removeImageFile($this->user);
        $this->assertFalse($this->storage->has($fileName));
        $this->assertNull($this->user->getImageExtension());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var UserImageFilesystem $UserImageFilesystem */
        $UserImageFilesystem = $container->get(UserImageFilesystem::class);
        $this->userImageFilesystem = $UserImageFilesystem;

        /** @var FilesystemOperator $storage */
        $storage = $container->get('user_image.storage');
        $this->storage = $storage;

        $this->user = new User('bob@gmail.com');
        $this->userIdString = $this->user
            ->getId()
            ->toRfc4122()
        ;

        $this->imageFile = new FileMock('png', 'image.png', 'Content...');
    }
}