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
        $this->assertNotEmpty($result); // invalid

        $data->setAgeMin(1);
        $result = $validator->validateProperty($data, 'ageMin');
        $this->assertEmpty($result); // valid
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

    public function testAgeMax(): void
    {
        $data = new CampData();
        $this->assertNull($data->getAgeMax());

        $data->setAgeMax(4);
        $this->assertSame(4, $data->getAgeMax());

        $data->setAgeMax(null);
        $this->assertNull($data->getAgeMax());
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