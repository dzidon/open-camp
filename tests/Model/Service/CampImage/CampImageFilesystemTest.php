<?php

namespace App\Tests\Model\Service\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Service\CampImage\CampImageFilesystem;
use App\Tests\Service\Filesystem\FilesystemMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampImageFilesystemTest extends KernelTestCase
{
    private FilesystemMock $filesystemMock;
    private CampImageFilesystem $campImageFilesystem;
    private CampImage $campImage;
    private string $campImageIdString;

    public function testGetFilePath(): void
    {
        $path = $this->campImageFilesystem->getFilePath($this->campImage);

        $this->assertSame('img/dynamic/camp/' . $this->campImageIdString . '.png', $path);
    }

    public function testGetFilePathWithNullCampImage(): void
    {
        $path = $this->campImageFilesystem->getFilePath(null);

        $this->assertSame('no-image.jpg', $path);
    }

    public function testRemoveFile(): void
    {
        $this->campImageFilesystem->removeFile($this->campImage);

        $this->assertContains('kernel-dir/public/img/dynamic/camp/' . $this->campImageIdString . '.png', $this->filesystemMock->getRemovedFiles());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();
        $imageDirectory = $container->getParameter('app.camp_image_directory');

        $this->filesystemMock = new FilesystemMock();
        $this->campImageFilesystem = new CampImageFilesystem($this->filesystemMock, $imageDirectory, 'no-image.jpg', 'kernel-dir');
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campImage = new CampImage(100, 'png', $camp);
        $this->campImageIdString = $this->campImage
            ->getId()
            ->toRfc4122()
        ;
    }
}