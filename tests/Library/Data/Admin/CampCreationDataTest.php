<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampCreationData;
use App\Library\Data\Admin\CampData;
use App\Library\Data\Admin\CampDateData;
use DateTimeImmutable;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampCreationDataTest extends KernelTestCase
{
    public function testCampData(): void
    {
        $data = new CampCreationData();
        $this->assertInstanceOf(CampData::class, $data->getCampData());
    }

    public function testCampDatesData(): void
    {
        $data = new CampCreationData();
        $this->assertSame([], $data->getCampDatesData());

        $newCampDatesData = [
            new CampDateData(),
            new CampDateData(),
        ];

        foreach ($newCampDatesData as $newCampDateData)
        {
            $data->addCampDatesDatum($newCampDateData);
        }

        $this->assertSame($newCampDatesData, $data->getCampDatesData());

        $data->removeCampDatesDatum($newCampDatesData[0]);
        $this->assertNotContains($newCampDatesData[0], $data->getCampDatesData());
    }

    public function testImages(): void
    {
        $data = new CampCreationData();
        $this->assertSame([], $data->getImages());

        $newImages = [
            new UploadedFile('image1.png', 'original1.png', 'image/png', \UPLOAD_ERR_NO_FILE, true),
            new UploadedFile('image2.png', 'original2.png', 'image/png', \UPLOAD_ERR_NO_FILE, true),
        ];

        $data->setImages($newImages);
        $this->assertSame($newImages, $data->getImages());
    }

    public function testImagesConstraintPresence(): void
    {
        $data = new CampCreationData();

        $reflectionClass = new ReflectionClass($data);
        $property = $reflectionClass->getProperty('images');
        $attributes = $property->getAttributes();

        $attributeAll = null;

        foreach ($attributes as $attribute)
        {
            if ($attribute->getName() === All::class)
            {
                $attributeAll = $attribute;
            }
        }

        $this->assertSame(All::class, $attributeAll->getName());

        $nestedConstraints = $attributeAll->getArguments()[0];
        $constraintImage = null;

        foreach ($nestedConstraints as $nestedConstraint)
        {
            if ($nestedConstraint instanceof Image)
            {
                $constraintImage = $nestedConstraint;
            }
        }

        $this->assertInstanceOf(Image::class, $constraintImage);
    }

    public function testCampDatesCollisions(): void
    {
        $validator = $this->getValidator();

        $data = new CampCreationData();
        $campData = $data->getCampData();
        $campData->setName('Name');
        $campData->setUrlName('name');
        $campData->setAgeMin(1);
        $campData->setAgeMax(2);
        $campData->setStreet('Street 123');
        $campData->setTown('Town');
        $campData->setZip('12345');
        $campData->setCountry('CZ');

        $campDateData1 = new CampDateData();
        $campDateData1->setPrice(1000.0);
        $campDateData1->setCapacity(10);

        $campDateData2 = new CampDateData();
        $campDateData2->setPrice(2000.0);
        $campDateData2->setCapacity(20);

        $data->addCampDatesDatum($campDateData1);
        $data->addCampDatesDatum($campDateData2);

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-01'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-04'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-10'));
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-01'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-07'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-10'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-10'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-01'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-07'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-10'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-06'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-09'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-10'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-04'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-11'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-01'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-05'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-10'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-10'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-15'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-05'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-10'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDateData1->setStartAt(new DateTimeImmutable('2000-01-10'));
        $campDateData1->setEndAt(new DateTimeImmutable('2000-01-15'));
        $campDateData2->setStartAt(new DateTimeImmutable('2000-01-10'));
        $campDateData2->setEndAt(new DateTimeImmutable('2000-01-15'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}