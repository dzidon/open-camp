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
        $expectedLabel = 'Label';
        $expectedHelp = 'Help';
        $expectedRequiredType = AttachmentConfigRequiredTypeEnum::REQUIRED;
        $expectedMaxSize = 10.0;
        $expectedFileExtensions = [
            new FileExtension('png'),
            new FileExtension('jpg'),
        ];

        $attachmentConfig = new AttachmentConfig($expectedName, $expectedLabel, $expectedMaxSize);
        $attachmentConfig->setHelp($expectedHelp);
        $attachmentConfig->setRequiredType($expectedRequiredType);
        $attachmentConfig->setIsGlobal(true);

        foreach ($expectedFileExtensions as $expectedFileExtension)
        {
            $attachmentConfig->addFileExtension($expectedFileExtension);
        }

        $data = new AttachmentConfigData();
        $dataTransfer->fillData($data, $attachmentConfig);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedHelp, $data->getHelp());
        $this->assertSame($expectedLabel, $data->getLabel());
        $this->assertSame($expectedRequiredType, $data->getRequiredType());
        $this->assertSame($expectedMaxSize, $data->getMaxSize());
        $this->assertTrue($data->isGlobal());

        foreach ($data->getFileExtensionsData() as $key => $fileExtensionData)
        {
            $this->assertSame($expectedFileExtensions[$key]->getExtension(), $fileExtensionData->getExtension());
        }
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getAttachmentConfigDataTransfer();

        $expectedName = 'Config';
        $expectedLabel = 'Label';
        $expectedHelp = 'Help';
        $expectedRequiredType = AttachmentConfigRequiredTypeEnum::REQUIRED;
        $expectedMaxSize = 10.0;
        $expectedFileExtensionsData = [
            (new FileExtensionData())->setExtension('png'), // add existing
            (new FileExtensionData())->setExtension('txt'), // nothing
            (new FileExtensionData())->setExtension('txt'), // redundant extensions don't matter
            (new FileExtensionData())->setExtension('pdf'), // add new
        ];

        $attachmentConfig = new AttachmentConfig('', '', 0.0);
        $attachmentConfig->addFileExtension((new FileExtension('txt')));
        $attachmentConfig->addFileExtension((new FileExtension('jpg'))); // gets removed

        $data = new AttachmentConfigData();
        $data->setName($expectedName);
        $data->setLabel($expectedLabel);
        $data->setHelp($expectedHelp);
        $data->setRequiredType($expectedRequiredType);
        $data->setMaxSize($expectedMaxSize);
        $data->setIsGlobal(true);

        foreach ($expectedFileExtensionsData as $expectedFileExtensionData)
        {
            $data->addFileExtensionData($expectedFileExtensionData);
        }

        $dataTransfer->fillEntity($data, $attachmentConfig);

        $this->assertSame($expectedName, $attachmentConfig->getName());
        $this->assertSame($expectedLabel, $attachmentConfig->getLabel());
        $this->assertSame($expectedHelp, $attachmentConfig->getHelp());
        $this->assertSame($expectedRequiredType, $attachmentConfig->getRequiredType());
        $this->assertSame($expectedMaxSize, $attachmentConfig->getMaxSize());
        $this->assertCount(3, $attachmentConfig->getFileExtensions());
        $this->assertTrue($attachmentConfig->isGlobal());

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