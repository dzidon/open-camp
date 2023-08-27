<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampImageData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampImageDataTest extends KernelTestCase
{
    public function testPriority(): void
    {
        $data = new CampImageData();
        $this->assertSame(0, $data->getPriority());

        $data->setPriority(100);
        $this->assertSame(100, $data->getPriority());
    }

    public function testPriorityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampImageData();
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