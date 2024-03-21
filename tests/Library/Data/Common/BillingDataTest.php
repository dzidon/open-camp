<?php

namespace App\Tests\Library\Data\Common;

use App\Library\Data\Common\BillingData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BillingDataTest extends KernelTestCase
{
    public function testNameFirst(): void
    {
        $data = new BillingData(true);
        $this->assertNull($data->getNameFirst());

        $data->setNameFirst('text');
        $this->assertSame('text', $data->getNameFirst());

        $data->setNameFirst(null);
        $this->assertNull($data->getNameFirst());
    }

    public function testNameFirstValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst('');
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst(null);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameLast('Doe');
        $data->setNameFirst('');
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst(null);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNameLast(): void
    {
        $data = new BillingData(true);
        $this->assertNull($data->getNameLast());

        $data->setNameLast('text');
        $this->assertSame('text', $data->getNameLast());

        $data->setNameLast(null);
        $this->assertNull($data->getNameLast());
    }

    public function testNameLastValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast('');
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast(null);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameFirst('John');
        $data->setNameLast('');
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast(null);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid
    }

    public function testStreet(): void
    {
        $data = new BillingData(true);
        $this->assertSame(null, $data->getStreet());

        $data->setStreet('text');
        $this->assertSame('text', $data->getStreet());

        $data->setStreet(null);
        $this->assertSame(null, $data->getStreet());
    }

    public function testStreetValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

        $data->setStreet('foo');
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

        $data->setStreet('foo bar');
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

        $data->setStreet('1 1');
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

        $data->setStreet('foo123');
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

        $data->setStreet('123foo');
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

        $data->setStreet('foo 123');
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet('foo 123/a');
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet('123 foo');
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet('123/a foo');
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet('123 West 2nd Ave');
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid
    }

    public function testTown(): void
    {
        $data = new BillingData(true);
        $this->assertSame(null, $data->getTown());

        $data->setTown('text');
        $this->assertSame('text', $data->getTown());

        $data->setTown(null);
        $this->assertSame(null, $data->getTown());
    }

    public function testTownValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $result = $validator->validateProperty($data, 'town');
        $this->assertEmpty($result); // valid

        $data->setTown(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'town');
        $this->assertEmpty($result); // valid

        $data->setTown(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'town');
        $this->assertNotEmpty($result); // invalid
    }

    public function testCountry(): void
    {
        $data = new BillingData(true);
        $this->assertNull($data->getCountry());

        $data->setCountry('text');
        $this->assertSame('text', $data->getCountry());

        $data->setCountry(null);
        $this->assertNull($data->getCountry());
    }

    public function testCountryValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $result = $validator->validateProperty($data, 'country');
        $this->assertEmpty($result); // valid

        $data->setCountry('XX');
        $result = $validator->validateProperty($data, 'country');
        $this->assertNotEmpty($result); // invalid

        $data->setCountry('CZ');
        $result = $validator->validateProperty($data, 'country');
        $this->assertEmpty($result); // valid
    }

    public function testZip(): void
    {
        $data = new BillingData(true);
        $this->assertSame(null, $data->getZip());

        $data->setZip('text');
        $this->assertSame('text', $data->getZip());

        $data->setZip(null);
        $this->assertSame(null, $data->getZip());
    }

    public function testZipValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('12345');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 45');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 456789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123456789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('12345 6789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('12345-6789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 45 6789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 45-6789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid


        $data->setZip('123450');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 450');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('12345 67890');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 45 67890');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 45-67890');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid


        $data->setZip('1234');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 4');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('12345 678');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 45 678');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 45-678');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid


        $data->setZip('xxxxx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxx xx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxxxx xxxx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxx xx xxxx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxx xx-xxxx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid
    }

    public function testIsCompany(): void
    {
        $data = new BillingData(true);
        $this->assertFalse($data->isCompany());

        $data->setIsCompany(true);
        $this->assertTrue($data->isCompany());

        $data->setIsCompany(false);
        $this->assertFalse($data->isCompany());
    }

    public function testBusinessName(): void
    {
        $data = new BillingData(true);
        $this->assertSame(null, $data->getBusinessName());

        $data->setBusinessName('text');
        $this->assertSame('text', $data->getBusinessName());

        $data->setBusinessName(null);
        $this->assertSame(null, $data->getBusinessName());
    }

    public function testBusinessNameValidationIfCompanyIsTrue(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $data->setIsCompany(true);

        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName('');
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(null);
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertNotEmpty($result); // invalid
    }

    public function testBusinessNameValidationIfCompanyIsFalse(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $data->setIsCompany(false);

        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName('');
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(null);
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid
    }

    public function testBusinessNameValidationIfEuBusinessDataIsDisabled(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(false);
        $data->setIsCompany(true);

        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName('');
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(null);
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid

        $data->setBusinessName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'businessName');
        $this->assertEmpty($result); // valid
    }

    public function testBusinessCin(): void
    {
        $data = new BillingData(true);
        $this->assertSame(null, $data->getBusinessCin());

        $data->setBusinessCin('text');
        $this->assertSame('text', $data->getBusinessCin());

        $data->setBusinessCin(null);
        $this->assertSame(null, $data->getBusinessCin());
    }

    public function testBusinessCinValidationIfCompanyIsTrue(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $data->setIsCompany(true);

        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessCin('');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessCin(null);
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessCin('12345678');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('123456789');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessCin('1234567');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessCin('text');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertNotEmpty($result); // invalid
    }

    public function testBusinessCinValidationIfCompanyIsFalse(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $data->setIsCompany(false);

        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin(null);
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('12345678');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('123456789');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('1234567');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('text');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid
    }

    public function testBusinessCinValidationIfEuBusinessDataIsDisabled(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(false);
        $data->setIsCompany(true);

        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin(null);
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('12345678');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('123456789');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('1234567');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid

        $data->setBusinessCin('text');
        $result = $validator->validateProperty($data, 'businessCin');
        $this->assertEmpty($result); // valid
    }

    public function testBusinessVatId(): void
    {
        $data = new BillingData(true);
        $this->assertSame(null, $data->getBusinessVatId());

        $data->setBusinessVatId('text');
        $this->assertSame('text', $data->getBusinessVatId());

        $data->setBusinessVatId(null);
        $this->assertSame(null, $data->getBusinessVatId());
    }

    public function testBusinessVatIdValidationIfCompanyIsTrue(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $data->setIsCompany(true);

        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessVatId('');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessVatId(null);
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessVatId('CZ1234567');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessVatId('CZ12345678');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ123456789');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ1234567890');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ12345678901');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessVatId('SK123456789');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid

        $data->setBusinessVatId('SK1234567890');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK12345678901');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertNotEmpty($result); // invalid
    }

    public function testBusinessVatIdValidationIfCompanyIsFalse(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(true);
        $data->setIsCompany(false);

        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId(null);
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ1234567');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ12345678');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ123456789');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ1234567890');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ12345678901');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK123456789');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK1234567890');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK12345678901');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid
    }

    public function testBusinessVatIdValidationIfEuBusinessDataIsDisabled(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData(false);
        $data->setIsCompany(true);

        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId(null);
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ1234567');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ12345678');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ123456789');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ1234567890');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('CZ12345678901');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK123456789');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK1234567890');
        $result = $validator->validateProperty($data, 'businessVatId');
        $this->assertEmpty($result); // valid

        $data->setBusinessVatId('SK12345678901');
        $result = $validator->validateProperty($data, 'businessVatId');
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