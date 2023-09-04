<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ProfileData;
use App\Model\Entity\User;
use App\Service\Data\Transfer\Admin\ProfileDataTransfer;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getProfileDataTransfer();

        $expectedLeaderPhoneNumber = new PhoneNumber();
        $expectedLeaderPhoneNumber->setCountryCode(420);
        $expectedLeaderPhoneNumber->setNationalNumber('724888999');

        $user = new User('bob@gmail.com');
        $user->setLeaderPhoneNumber($expectedLeaderPhoneNumber);

        $data = new ProfileData();
        $dataTransfer->fillData($data, $user);

        $phoneNumber = $data->getLeaderPhoneNumber();
        $this->assertSame(420, $phoneNumber->getCountryCode());
        $this->assertSame('724888999', $phoneNumber->getNationalNumber());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getProfileDataTransfer();

        $expectedLeaderPhoneNumber = new PhoneNumber();
        $expectedLeaderPhoneNumber->setCountryCode(420);
        $expectedLeaderPhoneNumber->setNationalNumber('724888999');

        $user = new User('bob@gmail.com');

        $data = new ProfileData();
        $data->setLeaderPhoneNumber($expectedLeaderPhoneNumber);

        $dataTransfer->fillEntity($data, $user);

        $phoneNumber = $user->getLeaderPhoneNumber();
        $this->assertSame(420, $phoneNumber->getCountryCode());
        $this->assertSame('724888999', $phoneNumber->getNationalNumber());
    }

    private function getProfileDataTransfer(): ProfileDataTransfer
    {
        $container = static::getContainer();

        /** @var ProfileDataTransfer $dataTransfer */
        $dataTransfer = $container->get(ProfileDataTransfer::class);

        return $dataTransfer;
    }
}