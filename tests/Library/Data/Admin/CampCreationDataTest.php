<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampCreationData;
use App\Library\Data\Admin\CampData;
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

    public function testCampDataValidation(): void
    {
        $data = new CampCreationData();
        $validator = $this->getValidator();

        $result = $validator->validateProperty($data, 'campData');
        $this->assertNotEmpty($result); // invalid

        $campData = $data->getCampData();
        $campData->setName('Camp');
        $campData->setUrlName('camp');
        $campData->setAgeMin(5);
        $campData->setAgeMax(10);
        $campData->setStreet('Street 123');
        $campData->setTown('Town');
        $campData->setZip('12345');
        $campData->setCountry('DE');

        $result = $validator->validateProperty($data, 'campData');
        $this->assertEmpty($result); // valid
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

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}