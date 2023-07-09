<?php

namespace App\Tests\Form\DataTransfer\Transfer\User;

use App\Form\DataTransfer\Data\User\BillingData;
use App\Form\DataTransfer\Transfer\User\BillingDataTransfer;
use App\Model\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BillingDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer();

        $expectedName = 'Name';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';

        $user = new User('bob@gmail.com');
        $user->setName($expectedName);
        $user->setStreet($expectedStreet);
        $user->setTown($expectedTown);
        $user->setZip($expectedZip);
        $user->setCountry($expectedCountry);

        $data = new BillingData();
        $dataTransfer->fillData($data, $user);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedStreet, $data->getStreet());
        $this->assertSame($expectedTown, $data->getTown());
        $this->assertSame($expectedZip, $data->getZip());
        $this->assertSame($expectedCountry, $data->getCountry());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer();

        $expectedName = 'Name';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';

        $data = new BillingData();
        $data->setName($expectedName);
        $data->setStreet($expectedStreet);
        $data->setTown($expectedTown);
        $data->setZip($expectedZip);
        $data->setCountry($expectedCountry);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertSame($expectedName, $user->getName());
        $this->assertSame($expectedStreet, $user->getStreet());
        $this->assertSame($expectedTown, $user->getTown());
        $this->assertSame($expectedZip, $user->getZip());
        $this->assertSame($expectedCountry, $user->getCountry());
    }

    private function getProfileBillingDataTransfer(): BillingDataTransfer
    {
        $container = static::getContainer();

        /** @var BillingDataTransfer $dataTransfer */
        $dataTransfer = $container->get(BillingDataTransfer::class);

        return $dataTransfer;
    }
}