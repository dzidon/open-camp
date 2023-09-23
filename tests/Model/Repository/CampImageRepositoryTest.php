<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Repository\CampImageRepository;
use App\Model\Repository\CampRepositoryInterface;
use App\Tests\Service\Filesystem\FilesystemMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\UuidV4;

class CampImageRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $filesystemMock = new FilesystemMock();
        $container = static::getContainer();
        $container->set(Filesystem::class, $filesystemMock);

        $campImageRepository = $this->getCampImageRepository();
        $campRepository = $this->getCampRepository();

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');
        $campImage = new CampImage(100, 'png', $camp);
        $campRepository->saveCamp($camp, false);
        $campImageRepository->saveCampImage($campImage, true);
        $id = $campImage->getId();

        $loadedCampImage = $campImageRepository->find($id);
        $this->assertNotNull($loadedCampImage);
        $this->assertSame($campImage->getId(), $loadedCampImage->getId());

        $campImageRepository->removeCampImage($campImage, true);
        $loadedCampImage = $campImageRepository->find($id);
        $this->assertNull($loadedCampImage);

        $fileName = $id->toRfc4122() . '.png';
        $removedFilePaths = $filesystemMock->getRemovedFiles();
        $this->assertCount(1, $removedFilePaths);

        $removedFilePath = $removedFilePaths[0];
        $this->assertStringEndsWith($fileName, $removedFilePath);
    }

    public function testFindOneById(): void
    {
        $campImageRepository = $this->getCampImageRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camp = $campImageRepository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $camp->getId()->toRfc4122());
    }

    public function testFindByCamp(): void
    {
        $campImageRepository = $this->getCampImageRepository();
        $campRepository = $this->getCampRepository();

        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $campImages = $campImageRepository->findByCamp($camp);

        $idStrings = $this->getCampImageIdStrings($campImages);
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000', 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    public function testGetAdminPaginator(): void
    {
        $campImageRepository = $this->getCampImageRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $paginator = $campImageRepository->getAdminPaginator($camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampImageIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000', 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    private function getCampImageIdStrings(array $campImages): array
    {
        $idStrings = [];

        /** @var CampImage $campImage */
        foreach ($campImages as $campImage)
        {
            $idStrings[] = $campImage
                ->getId()
                ->toRfc4122()
            ;
        }

        return $idStrings;
    }

    private function getCampRepository(): CampRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampRepositoryInterface $repository */
        $repository = $container->get(CampRepositoryInterface::class);

        return $repository;
    }

    private function getCampImageRepository(): CampImageRepository
    {
        $container = static::getContainer();

        /** @var CampImageRepository $repository */
        $repository = $container->get(CampImageRepository::class);

        return $repository;
    }
}