<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Form\DataTransfer\Data\User\ProfilePasswordChangeData;
use PHPUnit\Framework\TestCase;

class ProfilePasswordChangeDataTest extends TestCase
{
    public function testPlainPassword(): void
    {
        $data = new ProfilePasswordChangeData();
        $this->assertSame('', $data->getCurrentPassword());

        $data->setCurrentPassword(null);
        $this->assertSame('', $data->getCurrentPassword());

        $data->setCurrentPassword('text');
        $this->assertSame('text', $data->getCurrentPassword());
    }
}