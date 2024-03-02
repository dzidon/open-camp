<?php

namespace App\Tests\Model\Service\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Service\CampImage\CampImageFilesystem;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampImageFilesystemTest extends KernelTestCase
{
    private FilesystemOperator $storage;

    private CampImageFilesystem $campImageFilesystem;

    private CampImage $campImage;

    private CampImage $campImageWithNonExistentFile;

    private string $campImageIdString;

    private string $campImageFileName;

    public function testGetImageLastModified(): void
    {
        $this->assertSame(time(), $this->campImageFilesystem->getImageLastModified($this->campImage));
    }

    public function testGetImageLastModifiedForNonexistentFile(): void
    {
        $this->assertNull($this->campImageFilesystem->getImageLastModified($this->campImageWithNonExistentFile));
    }

    public function testIsUrlPlaceholder(): void
    {
        $this->assertTrue($this->campImageFilesystem->isUrlPlaceholder('/files/static/placeholder.jpg'));
        $this->assertTrue($this->campImageFilesystem->isUrlPlaceholder('/files/static/placeholder.jpg?foo=bar&xyz=123'));
        $this->assertFalse($this->campImageFilesystem->isUrlPlaceholder('/a/b/c'));
    }

    public function testGetImagePublicUrl(): void
    {
        $actualUrl = $this->campImageFilesystem->getImagePublicUrl($this->campImage);
        $expectedUrl = '/files/dynamic/camp/' . $this->campImageIdString . '.png';

        $this->assertSame($expectedUrl, $actualUrl);
    }

    public function testGetImagePublicUrlWithNonexistentFile(): void
    {
        $actualUrl = $this->campImageFilesystem->getImagePublicUrl($this->campImageWithNonExistentFile);

        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);
    }

    public function testGetImagePublicUrlWithNullCampImage(): void
    {
        $actualUrl = $this->campImageFilesystem->getImagePublicUrl(null);

        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);
    }

    public function testRemoveFile(): void
    {
        $this->campImageFilesystem->removeFile($this->campImage);

        $this->assertFalse($this->storage->has($this->campImageFileName));
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var CampImageFilesystem $campImageFilesystem */
        $campImageFilesystem = $container->get(CampImageFilesystem::class);
        $this->campImageFilesystem = $campImageFilesystem;

        /** @var FilesystemOperator $storage */
        $storage = $container->get('camp_image.storage');
        $this->storage = $storage;

        $camp = new Camp('Camp', 'camp', 5, 10, 321);
        $this->campImage = new CampImage(100, 'png', $camp);
        $this->campImageIdString = $this->campImage
            ->getId()
            ->toRfc4122()
        ;

        $this->campImageFileName = $this->campImageIdString . '.png';
        $this->storage->write($this->campImageFileName, 'Contents...');

        $this->campImageWithNonExistentFile = new CampImage(100, 'jpg', $camp);
    }
}