<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampDateAttachmentConfigDataTest extends KernelTestCase
{
    public function testAttachmentConfig(): void
    {
        $data = new CampDateAttachmentConfigData();
        $this->assertNull($data->getAttachmentConfig());

        $attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $data->setAttachmentConfig($attachmentConfig);
        $this->assertSame($attachmentConfig, $data->getAttachmentConfig());

        $data->setAttachmentConfig(null);
        $this->assertNull($data->getAttachmentConfig());
    }

    public function testAttachmentConfigValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateAttachmentConfigData();
        $result = $validator->validateProperty($data, 'attachmentConfig');
        $this->assertNotEmpty($result); // invalid

        $attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $data->setAttachmentConfig($attachmentConfig);
        $result = $validator->validateProperty($data, 'attachmentConfig');
        $this->assertEmpty($result); // valid

        $data->setAttachmentConfig(null);
        $result = $validator->validateProperty($data, 'attachmentConfig');
        $this->assertNotEmpty($result); // invalid
    }

    public function testPriority(): void
    {
        $data = new CampDateAttachmentConfigData();
        $this->assertSame(0, $data->getPriority());

        $data->setPriority(100);
        $this->assertSame(100, $data->getPriority());

        $data->setPriority(null);
        $this->assertNull($data->getPriority());
    }

    public function testPriorityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateAttachmentConfigData();
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(100);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(null);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertNotEmpty($result); // invalid
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}