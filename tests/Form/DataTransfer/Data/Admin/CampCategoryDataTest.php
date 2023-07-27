<?php

namespace App\Tests\Form\DataTransfer\Data\Admin;

use App\Form\DataTransfer\Data\Admin\CampCategoryData;
use App\Model\Entity\CampCategory;
use App\Model\Repository\CampCategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampCategoryDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new CampCategoryData();
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
    }

    public function testName(): void
    {
        $data = new CampCategoryData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampCategoryData();
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
        $data = new CampCategoryData();
        $this->assertNull($data->getUrlName());

        $data->setUrlName('text');
        $this->assertSame('text', $data->getUrlName());

        $data->setUrlName(null);
        $this->assertNull($data->getUrlName());
    }

    public function testUrlNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampCategoryData();
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

        $data->setUrlName('nová-kategorie');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('nova kategorie');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('nova-kategorie');
        $result = $validator->validateProperty($data, 'urlName');
        $this->assertEmpty($result); // valid
    }

    public function testParent(): void
    {
        $data = new CampCategoryData();
        $this->assertNull($data->getParent());

        $parent = new CampCategory('Category', 'category');
        $data->setParent($parent);

        $this->assertSame($parent, $data->getParent());
    }

    public function testUniqueValidationNoParent(): void
    {
        $validator = $this->getValidator();
        $repository = $this->getCampCategoryRepository();

        $data = new CampCategoryData();
        $data->setName('name');
        $data->setUrlName('category');

        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setParent(null);
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setUrlName('category-1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('category-2');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $category1 = $repository->findByUrlName('category-1')[0];
        $data->setId($category1->getId());
        $data->setUrlName('category-1');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    public function testUniqueValidationWithParent(): void
    {
        $validator = $this->getValidator();
        $repository = $this->getCampCategoryRepository();
        $parent = $repository->findByUrlName('category-1')[0];

        $data = new CampCategoryData();
        $data->setName('name');
        $data->setUrlName('category');
        $data->setParent($parent);

        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setUrlName('category-2');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setUrlName('category-1');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $category2 = $repository->findByUrlName('category-2')[0];
        $data->setId($category2->getId());
        $data->setUrlName('category-2');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    private function getCampCategoryRepository(): CampCategoryRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampCategoryRepositoryInterface $repository */
        $repository = $container->get(CampCategoryRepositoryInterface::class);

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