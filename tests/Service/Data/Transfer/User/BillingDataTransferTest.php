<?php

namespace App\Tests\Service\Data\Transfer\User;

use App\Library\Data\User\BillingData;
use App\Model\Entity\User;
use App\Service\Data\Transfer\User\BillingDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BillingDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer(true);

        $expectedNameFirst = 'John';
        $expectedNameLast = 'Doe';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';
        $expectedBusinessName = 'Business';
        $expectedBusinessCin = '12345678';
        $expectedBusinessVatId = 'CZ12345678';

        $user = new User('bob@gmail.com');
        $user->setNameFirst($expectedNameFirst);
        $user->setNameLast($expectedNameLast);
        $user->setStreet($expectedStreet);
        $user->setTown($expectedTown);
        $user->setZip($expectedZip);
        $user->setCountry($expectedCountry);
        $user->setBusinessName($expectedBusinessName);
        $user->setBusinessCin($expectedBusinessCin);
        $user->setBusinessVatId($expectedBusinessVatId);

        $data = new BillingData(true);
        $dataTransfer->fillData($data, $user);

        $this->assertSame($expectedNameFirst, $data->getNameFirst());
        $this->assertSame($expectedNameLast, $data->getNameLast());
        $this->assertSame($expectedStreet, $data->getStreet());
        $this->assertSame($expectedTown, $data->getTown());
        $this->assertSame($expectedZip, $data->getZip());
        $this->assertSame($expectedCountry, $data->getCountry());
        $this->assertSame($expectedBusinessName, $data->getBusinessName());
        $this->assertSame($expectedBusinessCin, $data->getBusinessCin());
        $this->assertSame($expectedBusinessVatId, $data->getBusinessVatId());
        $this->assertTrue($data->isCompany());
    }

    public function testFillDataNoCompany(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer(true);

        $user = new User('bob@gmail.com');
        $user->setBusinessName(null);
        $user->setBusinessCin(null);
        $user->setBusinessVatId(null);

        $data = new BillingData(true);
        $dataTransfer->fillData($data, $user);

        $this->assertNull($data->getBusinessName());
        $this->assertNull($data->getBusinessCin());
        $this->assertNull($data->getBusinessVatId());
        $this->assertFalse($data->isCompany());
    }

    public function testFillDataIfEuBusinessDataIsDisabled(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer(false);

        $user = new User('bob@gmail.com');
        $user->setBusinessName('Name');
        $user->setBusinessCin('1234');
        $user->setBusinessVatId('4321');

        $data = new BillingData(false);
        $dataTransfer->fillData($data, $user);

        $this->assertNull($data->getBusinessName());
        $this->assertNull($data->getBusinessCin());
        $this->assertNull($data->getBusinessVatId());
        $this->assertFalse($data->isCompany());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer(true);

        $expectedNameFirst = 'John';
        $expectedNameLast = 'Doe';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';
        $expectedBusinessName = 'Business';
        $expectedBusinessCin = '12345678';
        $expectedBusinessVatId = 'CZ12345678';

        $data = new BillingData(true);
        $data->setNameFirst($expectedNameFirst);
        $data->setNameLast($expectedNameLast);
        $data->setStreet($expectedStreet);
        $data->setTown($expectedTown);
        $data->setZip($expectedZip);
        $data->setCountry($expectedCountry);
        $data->setBusinessName($expectedBusinessName);
        $data->setBusinessCin($expectedBusinessCin);
        $data->setBusinessVatId($expectedBusinessVatId);
        $data->setIsCompany(true);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertSame($expectedNameFirst, $user->getNameFirst());
        $this->assertSame($expectedNameLast, $user->getNameLast());
        $this->assertSame($expectedStreet, $user->getStreet());
        $this->assertSame($expectedTown, $user->getTown());
        $this->assertSame($expectedZip, $user->getZip());
        $this->assertSame($expectedCountry, $user->getCountry());
        $this->assertSame($expectedBusinessName, $user->getBusinessName());
        $this->assertSame($expectedBusinessCin, $user->getBusinessCin());
        $this->assertSame($expectedBusinessVatId, $user->getBusinessVatId());
    }

    public function testFillEntityNoCompany(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer(true);

        $data = new BillingData(true);
        $data->setBusinessName('Business');
        $data->setBusinessCin('12345678');
        $data->setBusinessVatId('CZ12345678');
        $data->setIsCompany(false);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertNull($user->getBusinessName());
        $this->assertNull($user->getBusinessCin());
        $this->assertNull($user->getBusinessVatId());
    }

    public function testFillEntityIfEuBusinessDataIsDisabled(): void
    {
        $dataTransfer = $this->getProfileBillingDataTransfer(false);

        $data = new BillingData(false);
        $data->setBusinessName('Business');
        $data->setBusinessCin('12345678');
        $data->setBusinessVatId('CZ12345678');
        $data->setIsCompany(true);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertNull($user->getBusinessName());
        $this->assertNull($user->getBusinessCin());
        $this->assertNull($user->getBusinessVatId());
    }

    private function getProfileBillingDataTransfer(bool $isEuBusinessDataEnabled): BillingDataTransfer
    {
        return new BillingDataTransfer($isEuBusinessDataEnabled);
    }
}