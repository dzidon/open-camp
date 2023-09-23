<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\AttachmentConfigSearchData;
use App\Library\Enum\Search\Data\Admin\AttachmentConfigSortEnum;
use App\Model\Entity\AttachmentConfig;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use App\Model\Repository\AttachmentConfigRepository;
use App\Model\Repository\FileExtensionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class AttachmentConfigRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $attachmentConfig = new AttachmentConfig('Config', 10.0);
        $attachmentConfigRepository->saveAttachmentConfig($attachmentConfig, true);
        $id = $attachmentConfig->getId();

        $loadedAttachmentConfig = $attachmentConfigRepository->find($id);
        $this->assertNotNull($loadedAttachmentConfig);
        $this->assertSame($attachmentConfig->getId(), $loadedAttachmentConfig->getId());

        $attachmentConfigRepository->removeAttachmentConfig($attachmentConfig, true);
        $loadedAttachmentConfig = $attachmentConfigRepository->find($id);
        $this->assertNull($loadedAttachmentConfig);
    }

    public function testFindOneById(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $attachmentConfig = $attachmentConfigRepository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $attachmentConfig->getId()->toRfc4122());
    }

    public function testFindOneByName(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $attachmentConfig = $attachmentConfigRepository->findOneByName('Text file');
        $this->assertSame('Text file', $attachmentConfig->getName());
    }

    public function testGetAdminPaginator(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Image', 'Text file'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setPhrase('text');

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Text file'], $names);
    }

    public function testGetAdminPaginatorWithRequiredType(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setRequiredType(AttachmentConfigRequiredTypeEnum::REQUIRED);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Image'], $names);
    }

    public function testGetAdminPaginatorWithFileExtensions(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();
        $fileExtensionRepository = $this->getFileExtensionRepository();
        $extensionPng = $fileExtensionRepository->findOneByExtension('png');

        $data = new AttachmentConfigSearchData();
        $data->addFileExtension($extensionPng);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Image'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setSortBy(AttachmentConfigSortEnum::CREATED_AT_DESC);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Image', 'Text file'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setSortBy(AttachmentConfigSortEnum::CREATED_AT_ASC);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Text file', 'Image'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setSortBy(AttachmentConfigSortEnum::NAME_ASC);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Image', 'Text file'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setSortBy(AttachmentConfigSortEnum::NAME_DESC);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Text file', 'Image'], $names);
    }

    public function testGetAdminPaginatorSortByMaxSizeAsc(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setSortBy(AttachmentConfigSortEnum::MAX_SIZE_ASC);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Text file', 'Image'], $names);
    }

    public function testGetAdminPaginatorSortByMaxSizeDesc(): void
    {
        $attachmentConfigRepository = $this->getAttachmentConfigRepository();

        $data = new AttachmentConfigSearchData();
        $data->setSortBy(AttachmentConfigSortEnum::MAX_SIZE_DESC);

        $paginator = $attachmentConfigRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getConfigNames($paginator->getCurrentPageItems());
        $this->assertSame(['Image', 'Text file'], $names);
    }

    private function getConfigNames(array $configs): array
    {
        $names = [];

        /** @var AttachmentConfig $config */
        foreach ($configs as $config)
        {
            $names[] = $config->getName();
        }

        return $names;
    }

    private function getFileExtensionRepository(): FileExtensionRepositoryInterface
    {
        $container = static::getContainer();

        /** @var FileExtensionRepositoryInterface $repository */
        $repository = $container->get(FileExtensionRepositoryInterface::class);

        return $repository;
    }

    private function getAttachmentConfigRepository(): AttachmentConfigRepository
    {
        $container = static::getContainer();

        /** @var AttachmentConfigRepository $repository */
        $repository = $container->get(AttachmentConfigRepository::class);

        return $repository;
    }
}