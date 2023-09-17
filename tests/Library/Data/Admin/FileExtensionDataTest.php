<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\FileExtensionData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileExtensionDataTest extends KernelTestCase
{
    public function testExtension(): void
    {
        $data = new FileExtensionData();
        $this->assertNull($data->getExtension());

        $data->setExtension('text');
        $this->assertSame('text', $data->getExtension());

        $data->setExtension(null);
        $this->assertNull($data->getExtension());
    }

    public function testExtensionValidation(): void
    {
        $validator = $this->getValidator();

        $data = new FileExtensionData();
        $result = $validator->validateProperty($data, 'extension');
        $this->assertNotEmpty($result); // invalid

        $data->setExtension('');
        $result = $validator->validateProperty($data, 'extension');
        $this->assertNotEmpty($result); // invalid

        $data->setExtension(null);
        $result = $validator->validateProperty($data, 'extension');
        $this->assertNotEmpty($result); // invalid

        $data->setExtension(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'extension');
        $this->assertEmpty($result); // valid

        $data->setExtension(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'extension');
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