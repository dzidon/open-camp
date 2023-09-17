<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Library\Data\Admin\FileExtensionData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Repository\FileExtensionRepositoryInterface;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link AttachmentConfigData} to {@link AttachmentConfig} and vice versa.
 */
class AttachmentConfigDataTransfer implements DataTransferInterface
{
    private FileExtensionRepositoryInterface $fileExtensionRepository;

    public function __construct(FileExtensionRepositoryInterface $fileExtensionRepository)
    {
        $this->fileExtensionRepository = $fileExtensionRepository;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof AttachmentConfigData && $entity instanceof AttachmentConfig;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var AttachmentConfigData $attachmentConfigData */
        /** @var AttachmentConfig $attachmentConfig */
        $attachmentConfigData = $data;
        $attachmentConfig = $entity;

        $attachmentConfigData->setId($attachmentConfig->getId());
        $attachmentConfigData->setName($attachmentConfig->getName());
        $attachmentConfigData->setRequiredType($attachmentConfig->getRequiredType());
        $attachmentConfigData->setMaxSize($attachmentConfig->getMaxSize());

        foreach ($attachmentConfig->getFileExtensions() as $fileExtension)
        {
            $fileExtensionData = new FileExtensionData();
            $fileExtensionData->setExtension($fileExtension->getExtension());
            $attachmentConfigData->addFileExtensionsDatum($fileExtensionData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var AttachmentConfigData $attachmentConfigData */
        /** @var AttachmentConfig $attachmentConfig */
        $attachmentConfigData = $data;
        $attachmentConfig = $entity;

        $attachmentConfig->setName($attachmentConfigData->getName());
        $attachmentConfig->setRequiredType($attachmentConfigData->getRequiredType());
        $attachmentConfig->setMaxSize($attachmentConfigData->getMaxSize());

        $this->fillAttachmentConfigFileExtensions($attachmentConfigData, $attachmentConfig);
    }

    /**
     * Fills the file extension entity collection based on the submitted string extensions.
     *
     * @param AttachmentConfigData $attachmentConfigData
     * @param AttachmentConfig $attachmentConfig
     * @return void
     */
    private function fillAttachmentConfigFileExtensions(AttachmentConfigData $attachmentConfigData, AttachmentConfig $attachmentConfig): void
    {
        // get submitted extensions
        $dataExtensions = [];

        foreach ($attachmentConfigData->getFileExtensionsData() as $fileExtensionData)
        {
            $extension = $fileExtensionData->getExtension();
            $dataExtensions[$extension] = $extension;
        }

        // removal of extensions that were not submitted
        foreach ($attachmentConfig->getFileExtensions() as $fileExtension)
        {
            $extension = $fileExtension->getExtension();

            if (array_key_exists($extension, $dataExtensions))
            {
                unset($dataExtensions[$extension]); // make the $dataExtensions array contain only newly added extensions
            }
            else
            {
                $attachmentConfig->removeFileExtension($fileExtension);
            }
        }

        // add existing extensions
        $existingFileExtensions = $this->fileExtensionRepository->findByExtensions($dataExtensions);

        foreach ($existingFileExtensions as $fileExtension)
        {
            $attachmentConfig->addFileExtension($fileExtension);

            unset($dataExtensions[$fileExtension->getExtension()]);
        }

        // create and add new extensions
        foreach ($dataExtensions as $extension)
        {
            $newFileExtension = $this->fileExtensionRepository->createFileExtension($extension);
            $this->fileExtensionRepository->saveFileExtension($newFileExtension, false);
            $attachmentConfig->addFileExtension($newFileExtension);
        }
    }
}