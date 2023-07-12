<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Form\DataTransfer\Data\User\LoginData;
use PHPUnit\Framework\TestCase;

class LoginDataTest extends TestCase
{
    public function testEmail(): void
    {
        $data = new LoginData();
        $this->assertNull($data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());

        $data->setEmail(null);
        $this->assertNull($data->getEmail());
    }

    public function testPassword(): void
    {
        $data = new LoginData();
        $this->assertNull($data->getPassword());

        $data->setPassword('text');
        $this->assertSame('text', $data->getPassword());

        $data->setPassword(null);
        $this->assertNull($data->getPassword());
    }

    public function testRememberMe(): void
    {
        $data = new LoginData();
        $this->assertFalse($data->isRememberMe());

        $data->setRememberMe(true);
        $this->assertTrue($data->isRememberMe());
    }
}