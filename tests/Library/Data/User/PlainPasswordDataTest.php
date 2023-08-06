<?php

namespace App\Tests\Library\Data\User;

use App\Library\Data\User\PlainPasswordData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlainPasswordDataTest extends KernelTestCase
{
    public function testPlainPassword(): void
    {
        $data = new PlainPasswordData();
        $this->assertNull($data->getPlainPassword());

        $data->setPlainPassword('text');
        $this->assertSame('text', $data->getPlainPassword());

        $data->setPlainPassword(null);
        $this->assertNull($data->getPlainPassword());
    }

    public function testPlainPasswordValidation(): void
    {
        $validator = $this->getValidator();

        $data = new PlainPasswordData();
        $result = $validator->validateProperty($data, 'plainPassword');
        $this->assertNotEmpty($result); // invalid

        $data->setPlainPassword(null);
        $result = $validator->validateProperty($data, 'plainPassword');
        $this->assertNotEmpty($result); // invalid

        $data->setPlainPassword(str_repeat('x', 5));
        $result = $validator->validateProperty($data, 'plainPassword');
        $this->assertNotEmpty($result); // invalid

        $data->setPlainPassword(str_repeat('x', 6));
        $result = $validator->validateProperty($data, 'plainPassword');
        $this->assertEmpty($result); // valid

        $data->setPlainPassword(str_repeat('x', 4096));
        $result = $validator->validateProperty($data, 'plainPassword');
        $this->assertEmpty($result); // valid

        $data->setPlainPassword(str_repeat('x', 4097));
        $result = $validator->validateProperty($data, 'plainPassword');
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