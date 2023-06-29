<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Form\DataTransfer\Data\User\RegistrationData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationDataTest extends KernelTestCase
{
    public function testEmail(): void
    {
        $data = new RegistrationData();
        $this->assertSame('', $data->getEmail());

        $data->setEmail(null);
        $this->assertSame('', $data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());
    }

    public function testEmailValidation(): void
    {
        $validator = $this->getValidator();

        $data = new RegistrationData();
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(null);
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('abc');
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(str_repeat('x', 177) . '@a.b');
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(str_repeat('x', 176) . '@a.b');
        $result = $validator->validateProperty($data, 'email');
        $this->assertEmpty($result); // valid

        $data->setEmail('abc@gmail.com');
        $result = $validator->validateProperty($data, 'email');
        $this->assertEmpty($result); // valid
    }

    public function testCaptcha(): void
    {
        $data = new RegistrationData();
        $this->assertNull($data->getCaptcha());

        $data->setCaptcha('text');
        $this->assertSame('text', $data->getCaptcha());

        $data->setCaptcha(null);
        $this->assertNull($data->getCaptcha());
    }

    public function testPrivacy(): void
    {
        $data = new RegistrationData();
        $this->assertFalse($data->isPrivacy());

        $data->setPrivacy(true);
        $this->assertTrue($data->isPrivacy());
    }

    public function testPrivacyValidation(): void
    {
        $validator = $this->getValidator();

        $data = new RegistrationData();
        $result = $validator->validateProperty($data, 'privacy');
        $this->assertNotEmpty($result); // invalid

        $data->setPrivacy(true);
        $result = $validator->validateProperty($data, 'privacy');
        $this->assertEmpty($result); // valid
    }

    public function testTerms(): void
    {
        $data = new RegistrationData();
        $this->assertFalse($data->isTerms());

        $data->setTerms(true);
        $this->assertTrue($data->isTerms());
    }

    public function testTermsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new RegistrationData();
        $result = $validator->validateProperty($data, 'terms');
        $this->assertNotEmpty($result); // invalid

        $data->setTerms(true);
        $result = $validator->validateProperty($data, 'terms');
        $this->assertEmpty($result); // valid
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}