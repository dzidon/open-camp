<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Library\Data\Admin\FileExtensionData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttachmentConfigDataTest extends KernelTestCase
{
    public function testAttachmentConfig(): void
    {
        $data = new AttachmentConfigData();
        $this->assertNull($data->getAttachmentConfig());

        $attachmentConfig = new AttachmentConfig('Photo', 'Photo...', 10.0);

        $data = new AttachmentConfigData($attachmentConfig);
        $this->assertSame($attachmentConfig, $data->getAttachmentConfig());
    }

    public function testName(): void
    {
        $data = new AttachmentConfigData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new AttachmentConfigData();
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName('');
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(null);
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testLabel(): void
    {
        $data = new AttachmentConfigData();
        $this->assertNull($data->getLabel());

        $data->setLabel('label');
        $this->assertSame('label', $data->getLabel());

        $data->setLabel(null);
        $this->assertNull($data->getLabel());
    }

    public function testLabelValidation(): void
    {
        $validator = $this->getValidator();

        $data = new AttachmentConfigData();
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel('');
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(null);
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'label');
        $this->assertEmpty($result); // valid

        $data->setLabel(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid
    }

    public function testMaxSize(): void
    {
        $data = new AttachmentConfigData();
        $this->assertNull($data->getMaxSize());

        $data->setMaxSize(10.0);
        $this->assertSame(10.0, $data->getMaxSize());

        $data->setMaxSize(null);
        $this->assertNull($data->getMaxSize());
    }

    public function testMaxSizeValidation(): void
    {
        $validator = $this->getValidator();

        $data = new AttachmentConfigData();
        $result = $validator->validateProperty($data, 'maxSize');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxSize(null);
        $result = $validator->validateProperty($data, 'maxSize');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxSize(-1.0);
        $result = $validator->validateProperty($data, 'maxSize');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxSize(0.0);
        $result = $validator->validateProperty($data, 'maxSize');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxSize(1.0);
        $result = $validator->validateProperty($data, 'maxSize');
        $this->assertEmpty($result); // valid
    }

    public function testRequiredType(): void
    {
        $data = new AttachmentConfigData();
        $this->assertNull($data->getRequiredType());

        $data->setRequiredType(AttachmentConfigRequiredTypeEnum::REQUIRED);
        $this->assertSame(AttachmentConfigRequiredTypeEnum::REQUIRED, $data->getRequiredType());

        $data->setRequiredType(null);
        $this->assertNull($data->getRequiredType());
    }

    public function testRequiredTypeValidation(): void
    {
        $validator = $this->getValidator();

        $data = new AttachmentConfigData();
        $result = $validator->validateProperty($data, 'requiredType');
        $this->assertNotEmpty($result); // invalid

        $data->setRequiredType(null);
        $result = $validator->validateProperty($data, 'requiredType');
        $this->assertNotEmpty($result); // invalid

        $data->setRequiredType(AttachmentConfigRequiredTypeEnum::REQUIRED);
        $result = $validator->validateProperty($data, 'requiredType');
        $this->assertEmpty($result); // valid
    }

    public function testIsGlobal(): void
    {
        $data = new AttachmentConfigData();
        $this->assertFalse($data->isGlobal());

        $data->setIsGlobal(true);
        $this->assertTrue($data->isGlobal());

        $data->setIsGlobal(false);
        $this->assertFalse($data->isGlobal());
    }

    public function testFileExtensionsData(): void
    {
        $data = new AttachmentConfigData();
        $this->assertSame([], $data->getFileExtensionsData());

        $newFileExtensionsData = [
            new FileExtensionData(),
            new FileExtensionData(),
        ];

        foreach ($newFileExtensionsData as $newFileExtensionData)
        {
            $data->addFileExtensionData($newFileExtensionData);
        }

        $this->assertSame($newFileExtensionsData, $data->getFileExtensionsData());

        $data->removeFileExtensionData($newFileExtensionsData[0]);
        $this->assertNotContains($newFileExtensionsData[0], $data->getFileExtensionsData());
    }

    public function testFileExtensionsDataValidation(): void
    {
        $validator = $this->getValidator();

        $data = new AttachmentConfigData();
        $result = $validator->validateProperty($data, 'fileExtensionsData');
        $this->assertNotEmpty($result); // invalid

        $fileExtensionData = new FileExtensionData();
        $data->addFileExtensionData($fileExtensionData);
        $result = $validator->validateProperty($data, 'fileExtensionsData');
        $this->assertNotEmpty($result); // invalid

        $fileExtensionData->setExtension('png');
        $result = $validator->validateProperty($data, 'fileExtensionsData');
        $this->assertEmpty($result); // valid
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new AttachmentConfigData();
        $data->setMaxSize(10.0);
        $data->setLabel('Label');
        $data->setRequiredType(AttachmentConfigRequiredTypeEnum::REQUIRED);
        $data->addFileExtensionData((new FileExtensionData())->setExtension('pdf'));
        $data->setName('Text file');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $attachmentConfigRepository = $this->getAttachmentConfigRepository();
        $attachmentConfig = $attachmentConfigRepository->findOneByName('Image');
        $data = new AttachmentConfigData($attachmentConfig);
        $data->setMaxSize(10.0);
        $data->setLabel('Label');
        $data->setRequiredType(AttachmentConfigRequiredTypeEnum::REQUIRED);
        $data->addFileExtensionData((new FileExtensionData())->setExtension('pdf'));
        $data->setName('Image');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setName('Text file');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    private function getAttachmentConfigRepository(): AttachmentConfigRepositoryInterface
    {
        $container = static::getContainer();

        /** @var AttachmentConfigRepositoryInterface $repository */
        $repository = $container->get(AttachmentConfigRepositoryInterface::class);

        return $repository;
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}