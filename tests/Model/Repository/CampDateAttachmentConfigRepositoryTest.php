<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use App\Model\Repository\CampDateAttachmentConfigRepository;
use App\Model\Repository\CampDateRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateAttachmentConfigRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $campDateAttachmentConfigRepository = $this->getCampDateAttachmentConfigRepository();
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();
        $campDateRepository = $this->getCampDateRepository();

        $campDate = $campDateRepository->findOneById(new UuidV4('550e8400-e29b-41d4-a716-446655440000'));
        $attachmentConfig = $attachmentConfigRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $campDateAttachmentConfig = new CampDateAttachmentConfig($campDate, $attachmentConfig, 300);
        $campDateAttachmentConfigRepository->saveCampDateAttachmentConfig($campDateAttachmentConfig, true);

        $loadedCampDateAttachmentConfigs = $campDateAttachmentConfigRepository->findByCampDate($campDate);
        $this->assertCount(1, $loadedCampDateAttachmentConfigs);
        $loadedCampDateAttachmentConfig = $loadedCampDateAttachmentConfigs[0];
        $this->assertSame($attachmentConfig, $loadedCampDateAttachmentConfig->getAttachmentConfig());
        $this->assertSame($campDate, $loadedCampDateAttachmentConfig->getCampDate());

        $campDateAttachmentConfigRepository->removeCampDateAttachmentConfig($campDateAttachmentConfig, true);
        $loadedCampDateAttachmentConfigs = $campDateAttachmentConfigRepository->findByCampDate($campDate);
        $this->assertCount(0, $loadedCampDateAttachmentConfigs);
    }

    public function testFindByCampDate(): void
    {
        $campDateAttachmentConfigRepository = $this->getCampDateAttachmentConfigRepository();
        $campDateRepository = $this->getCampDateRepository();

        $campDate = $campDateRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $loadedCampDateAttachmentConfigs = $campDateAttachmentConfigRepository->findByCampDate($campDate);
        $this->assertCount(2, $loadedCampDateAttachmentConfigs);
    }

    private function getAttachmentConfigRepository(): AttachmentConfigRepositoryInterface
    {
        $container = static::getContainer();

        /** @var AttachmentConfigRepositoryInterface $repository */
        $repository = $container->get(AttachmentConfigRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateRepository(): CampDateRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampDateRepositoryInterface $repository */
        $repository = $container->get(CampDateRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateAttachmentConfigRepository(): CampDateAttachmentConfigRepository
    {
        $container = static::getContainer();

        /** @var CampDateAttachmentConfigRepository $repository */
        $repository = $container->get(CampDateAttachmentConfigRepository::class);

        return $repository;
    }
}