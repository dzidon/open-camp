<?php

namespace App\Tests\Model\Service\CampImage;

use App\Model\Entity\Camp;
use App\Model\Service\CampImage\CampImageFactory;
use App\Tests\Library\Http\File\FileMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampImageFactoryTest extends KernelTestCase
{
    private CampImageFactory $campImageFactory;

    public function testCreateCampImage(): void
    {
        $fileMock = new FileMock('png');
        $camp = new Camp('Camp', 'camp', 5, 10, 321);
        $campImage = $this->campImageFactory->createCampImage($fileMock, 100, $camp);
        $idString = $campImage
            ->getId()
            ->toRfc4122()
        ;

        $this->assertSame('png', $campImage->getExtension());
        $this->assertSame(100, $campImage->getPriority());
        $this->assertSame($camp, $campImage->getCamp());

        $this->assertSame($idString . '.png', $fileMock->getMovedName());
        $this->assertStringEndsWith('files/dynamic/camp', $fileMock->getMovedDirectory());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var CampImageFactory $factory */
        $factory = $container->get(CampImageFactory::class);

        $this->campImageFactory = $factory;
    }
}