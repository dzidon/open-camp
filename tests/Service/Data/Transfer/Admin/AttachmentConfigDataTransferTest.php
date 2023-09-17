<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Library\Data\Admin\FileExtensionData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\FileExtension;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use App\Service\Data\Transfer\Admin\AttachmentConfigDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AttachmentConfigDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getAttachmentConfigDataTransfer();

        $expectedName = 'Config';
        $expectedRequiredType = AttachmentConfigRequiredTypeEnum::REQUIRED;
        $expectedMaxSize = 10.0;
        $expectedFileExtensions = [
            new FileExtension('png'),
            new FileExtension('jpg'),
        ];

        $attachmentConfig = new AttachmentConfig($expectedName, $expectedMaxSize);
        $attachmentConfig->setRequiredType($expectedRequiredType);

        foreach ($expectedFileExtensions as $expectedFileExtension)
        {
            $attachmentConfig->addFileExtension($expectedFileExtension);
        }

        $data = new AttachmentConfigData();
        $dataTransfer->fillData($data, $attachmentConfig);

        $this->assertSame($attachmentConfig->getId(), $data->getId());
        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedRequiredType, $data->getRequiredType());
        $this->assertSame($expectedMaxSize, $data->getMaxSize());

        foreach ($data->getFileExtensionsData() as $key => $fileExtensionData)
        {
            $this->assertSame($expectedFileExtensions[$key]->getExtension(), $fileExtensionData->getExtension());
        }
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getAttachmentConfigDataTransfer();

        $expectedName = 'Config';
        $expectedRequiredType = AttachmentConfigRequiredTypeEnum::REQUIRED;
        $expectedMaxSize = 10.0;
        $expectedFileExtensionsData = [
            (new FileExtensionData())->setExtension('png'), // add existing
            (new FileExtensionData())->setExtension('txt'), // nothing
            (new FileExtensionData())->setExtension('txt'), // redundant extensions don't matter
            (new FileExtensionData())->setExtension('pdf'), // add new
        ];

        $attachmentConfig = new AttachmentConfig('', 0.0);
        $attachmentConfig->addFileExtension((new FileExtension('txt')));
        $attachmentConfig->addFileExtension((new FileExtension('jpg'))); // gets removed

        $data = new AttachmentConfigData();
        $data->setName($expectedName);
        $data->setRequiredType($expectedRequiredType);
        $data->setMaxSize($expectedMaxSize);

        foreach ($expectedFileExtensionsData as $expectedFileExtensionData)
        {
            $data->addFileExtensionsDatum($expectedFileExtensionData);
        }

        $dataTransfer->fillEntity($data, $attachmentConfig);

        $this->assertSame($expectedName, $attachmentConfig->getName());
        $this->assertSame($expectedRequiredType, $attachmentConfig->getRequiredType());
        $this->assertSame($expectedMaxSize, $attachmentConfig->getMaxSize());
        $this->assertCount(3, $attachmentConfig->getFileExtensions());

        $extensions = [];
        foreach ($attachmentConfig->getFileExtensions() as $fileExtension)
        {
            $extensions[] = $fileExtension->getExtension();
        }

        $this->assertContains('txt', $extensions);
        $this->assertContains('png', $extensions);
        $this->assertContains('pdf', $extensions);
    }

    private function getAttachmentConfigDataTransfer(): AttachmentConfigDataTransfer
    {
        $container = static::getContainer();

        /** @var AttachmentConfigDataTransfer $dataTransfer */
        $dataTransfer = $container->get(AttachmentConfigDataTransfer::class);

        return $dataTransfer;
    }
}