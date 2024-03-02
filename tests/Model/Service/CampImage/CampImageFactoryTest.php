<?php

namespace App\Tests\Model\Service\CampImage;

use App\Model\Entity\Camp;
use App\Model\Service\CampImage\CampImageFactory;
use App\Tests\Library\Http\File\FileMock;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampImageFactoryTest extends KernelTestCase
{
    private CampImageFactory $campImageFactory;

    private FilesystemOperator $storage;

    public function testCreateCampImage(): void
    {
        $file = new FileMock('png', 'image.png', 'Content...');
        $camp = new Camp('Camp', 'camp', 5, 10, 321);

        $campImage = $this->campImageFactory->createCampImage($file, $camp, 100);
        $idString = $campImage
            ->getId()
            ->toRfc4122()
        ;

        $fileName = $idString . '.png';

        $this->assertSame('png', $campImage->getExtension());
        $this->assertSame(100, $campImage->getPriority());
        $this->assertSame($camp, $campImage->getCamp());
        $this->assertTrue($this->storage->has($fileName));
        $this->assertSame('Content...', $this->storage->read($fileName));
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var CampImageFactory $factory */
        $factory = $container->get(CampImageFactory::class);
        $this->campImageFactory = $factory;

        /** @var FilesystemOperator $storage */
        $storage = $container->get('camp_image.storage');
        $this->storage = $storage;
    }
}