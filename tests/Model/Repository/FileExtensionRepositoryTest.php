<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\FileExtension;
use App\Model\Repository\FileExtensionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileExtensionRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $fileExtensionRepository = $this->getFileExtensionRepository();

        $fileExtension = new FileExtension('exe');
        $fileExtensionRepository->saveFileExtension($fileExtension, true);
        $id = $fileExtension->getId();

        $loadedFileExtension = $fileExtensionRepository->find($id);
        $this->assertNotNull($loadedFileExtension);
        $this->assertSame($fileExtension->getId(), $loadedFileExtension->getId());

        $fileExtensionRepository->removeFileExtension($fileExtension, true);
        $loadedFileExtension = $fileExtensionRepository->find($id);
        $this->assertNull($loadedFileExtension);
    }

    public function testFindForAttachmentConfigs(): void
    {
        $fileExtensionRepository = $this->getFileExtensionRepository();

        $fileExtensions = $fileExtensionRepository->findForAttachmentConfigs();
        $extensionNames = $this->getFileExtensionNames($fileExtensions);

        $this->assertSame(['docx', 'jpg', 'png', 'txt'], $extensionNames);
    }

    public function testFindOneByExtension(): void
    {
        $fileExtensionRepository = $this->getFileExtensionRepository();

        $fileExtension = $fileExtensionRepository->findOneByExtension('png');
        $this->assertSame('png', $fileExtension->getExtension());
    }

    public function testFindByExtensions(): void
    {
        $fileExtensionRepository = $this->getFileExtensionRepository();

        $fileExtensions = $fileExtensionRepository->findByExtensions(['png', 'jpg']);
        $extensionNames = $this->getFileExtensionNames($fileExtensions);

        $this->assertSame(['jpg', 'png'], $extensionNames);
    }

    private function getFileExtensionNames(array $fileExtensions): array
    {
        $names = [];

        /** @var FileExtension $fileExtension */
        foreach ($fileExtensions as $fileExtension)
        {
            $names[] = $fileExtension->getExtension();
        }

        return $names;
    }

    private function getFileExtensionRepository(): FileExtensionRepository
    {
        $container = static::getContainer();

        /** @var FileExtensionRepository $repository */
        $repository = $container->get(FileExtensionRepository::class);

        return $repository;
    }
}