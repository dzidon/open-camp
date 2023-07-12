<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Form\DataTransfer\Data\User\PasswordChangeData;
use PHPUnit\Framework\TestCase;

class PasswordChangeDataTest extends TestCase
{
    public function testEmail(): void
    {
        $data = new PasswordChangeData();
        $this->assertNull($data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());

        $data->setEmail(null);
        $this->assertNull($data->getEmail());
    }

    public function testCaptcha(): void
    {
        $data = new PasswordChangeData();
        $this->assertNull($data->getCaptcha());

        $data->setCaptcha('text');
        $this->assertSame('text', $data->getCaptcha());

        $data->setCaptcha(null);
        $this->assertNull($data->getCaptcha());
    }
}