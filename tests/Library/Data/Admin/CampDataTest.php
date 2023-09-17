<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampData;
use App\Model\Entity\CampCategory;
use App\Model\Repository\CampRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new CampData();
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
    }

    public function testName(): void
    {
        $data = new CampData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName('');
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(null);
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testUrlName(): void
    {
        $data = new CampData();
        $this->assertNull($data->getUrlName());

        $data->setUrlName('text');
        $this->assertSame('text', $data->getUrlName());

        $data->setUrlName(null);
        $this->assertNull($data->getUrlName());
    }

    public function testUrlNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName(null);
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertEmpty($result); // valid

        $data->setUrlName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('nový-tábor');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('novy tabor');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('novy-tabor');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertEmpty($result); // valid
    }

    public function testAgeMin(): void
    {
        $data = new CampData();
        $this->assertNull($data->getAgeMin());

        $data->setAgeMin(2);
        $this->assertSame(2, $data->getAgeMin());

        $data->setAgeMin(null);
        $this->assertNull($data->getAgeMin());
    }

    public function testAgeMinValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'ageMin');
        $this->assertNotEmpty($result); // invalid

        $data->setAgeMin(0);
        $result = $validator->validateProperty($data, 'ageMin');
        $this->assertEmpty($result); // valid

        $data->setAgeMin(1);
        $result = $validator->validateProperty($data, 'ageMin');
        $this->assertEmpty($result); // valid
    }

    public function testAgeMax(): void
    {
        $data = new CampData();
        $this->assertNull($data->getAgeMax());

        $data->setAgeMax(4);
        $this->assertSame(4, $data->getAgeMax());

        $data->setAgeMax(null);
        $this->assertNull($data->getAgeMax());
    }

    public function testAgeMaxValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $data->setAgeMin(2);
        $result = $validator->validateProperty($data, 'ageMax');
        $this->assertNotEmpty($result); // invalid

        $data->setAgeMax(1);
        $result = $validator->validateProperty($data, 'ageMax');
        $this->assertNotEmpty($result); // invalid

        $data->setAgeMax(2);
        $result = $validator->validateProperty($data, 'ageMax');
        $this->assertEmpty($result); // valid

        $data->setAgeMax(3);
        $result = $validator->validateProperty($data, 'ageMax');
        $this->assertEmpty($result); // valid
    }


    public function testStreet(): void
    {
        $data = new CampData();
        $this->assertSame(null, $data->getStreet());

        $data->setStreet('text');
        $this->assertSame('text', $data->getStreet());

        $data->setStreet(null);
        $this->assertSame(null, $data->getStreet());
    }

    public function testStreetValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid

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
        $data = new CampData();
        $this->assertSame(null, $data->getTown());

        $data->setTown('text');
        $this->assertSame('text', $data->getTown());

        $data->setTown(null);
        $this->assertSame(null, $data->getTown());
    }

    public function testTownValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'town');
        $this->assertNotEmpty($result); // invalid

        $data->setTown(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'town');
        $this->assertEmpty($result); // valid

        $data->setTown(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'town');
        $this->assertNotEmpty($result); // invalid
    }

    public function testCountry(): void
    {
        $data = new CampData();
        $this->assertNull($data->getCountry());

        $data->setCountry('text');
        $this->assertSame('text', $data->getCountry());

        $data->setCountry(null);
        $this->assertNull($data->getCountry());
    }

    public function testCountryValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'country');
        $this->assertNotEmpty($result); // invalid

        $data->setCountry('XX');
        $result = $validator->validateProperty($data, 'country');
        $this->assertNotEmpty($result); // invalid

        $data->setCountry('CZ');
        $result = $validator->validateProperty($data, 'country');
        $this->assertEmpty($result); // valid
    }

    public function testZip(): void
    {
        $data = new CampData();
        $this->assertSame(null, $data->getZip());

        $data->setZip('text');
        $this->assertSame('text', $data->getZip());

        $data->setZip(null);
        $this->assertSame(null, $data->getZip());
    }

    public function testZipValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

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

    public function testDescriptionShort(): void
    {
        $data = new CampData();
        $this->assertNull($data->getDescriptionShort());

        $data->setDescriptionShort('text');
        $this->assertSame('text', $data->getDescriptionShort());

        $data->setDescriptionShort(null);
        $this->assertNull($data->getDescriptionShort());
    }

    public function testDescriptionShortValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'descriptionShort');
        $this->assertEmpty($result); // valid

        $data->setDescriptionShort('');
        $result = $validator->validateProperty($data, 'descriptionShort');
        $this->assertEmpty($result); // valid

        $data->setDescriptionShort(null);
        $result = $validator->validateProperty($data, 'descriptionShort');
        $this->assertEmpty($result); // valid

        $data->setDescriptionShort(str_repeat('x', 160));
        $result = $validator->validateProperty($data, 'descriptionShort');
        $this->assertEmpty($result); // valid

        $data->setDescriptionShort(str_repeat('x', 161));
        $result = $validator->validateProperty($data, 'descriptionShort');
        $this->assertNotEmpty($result); // invalid
    }

    public function testDescriptionLong(): void
    {
        $data = new CampData();
        $this->assertNull($data->getDescriptionLong());

        $data->setDescriptionLong('text');
        $this->assertSame('text', $data->getDescriptionLong());

        $data->setDescriptionLong(null);
        $this->assertNull($data->getDescriptionLong());
    }

    public function testDescriptionLongValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $result = $validator->validateProperty($data, 'descriptionLong');
        $this->assertEmpty($result); // valid

        $data->setDescriptionLong('');
        $result = $validator->validateProperty($data, 'descriptionLong');
        $this->assertEmpty($result); // valid

        $data->setDescriptionLong(null);
        $result = $validator->validateProperty($data, 'descriptionLong');
        $this->assertEmpty($result); // valid

        $data->setDescriptionLong(str_repeat('x', 5000));
        $result = $validator->validateProperty($data, 'descriptionLong');
        $this->assertEmpty($result); // valid

        $data->setDescriptionLong(str_repeat('x', 5001));
        $result = $validator->validateProperty($data, 'descriptionLong');
        $this->assertNotEmpty($result); // invalid
    }

    public function testFeaturedPriority(): void
    {
        $data = new CampData();
        $this->assertNull($data->getFeaturedPriority());

        $data->setFeaturedPriority(100);
        $this->assertSame(100, $data->getFeaturedPriority());

        $data->setFeaturedPriority(null);
        $this->assertNull($data->getFeaturedPriority());
    }

    public function testCampCategory(): void
    {
        $data = new CampData();
        $this->assertNull($data->getCampCategory());

        $campCategory = new CampCategory('Name', 'name');
        $data->setCampCategory($campCategory);
        $this->assertSame($campCategory, $data->getCampCategory());

        $data->setCampCategory(null);
        $this->assertNull($data->getCampCategory());
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampData();
        $data->setName('Name');
        $data->setAgeMin(1);
        $data->setAgeMax(2);
        $data->setStreet('Street 123');
        $data->setTown('Town');
        $data->setZip('12345');
        $data->setCountry('CZ');

        $data->setId(null);
        $data->setUrlName('camp-1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setId(null);
        $data->setUrlName('url-name');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $campRepository = $this->getCampRepository();
        $camp = $campRepository->findOneByUrlName('camp-1');
        $data->setId($camp->getId());
        $data->setUrlName('camp-1');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setId($camp->getId());
        $data->setUrlName('camp-2');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setId($camp->getId());
        $data->setUrlName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    private function getCampRepository(): CampRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampRepositoryInterface $repository */
        $repository = $container->get(CampRepositoryInterface::class);

        return $repository;
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}