<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\Camp;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class CampImagesUploadDataTest extends TestCase
{
    private Camp $camp;

    public function testImages(): void
    {
        $data = new CampImagesUploadData($this->camp);
        $this->assertSame([], $data->getImages());

        $newImages = [
            new UploadedFile('image1.png', 'original1.png', 'image/png', \UPLOAD_ERR_NO_FILE, true),
            new UploadedFile('image2.png', 'original2.png', 'image/png', \UPLOAD_ERR_NO_FILE, true),
        ];

        $data->setImages($newImages);
        $this->assertSame($newImages, $data->getImages());
    }

    public function testCamp(): void
    {
        $data = new CampImagesUploadData($this->camp);
        $this->assertSame($this->camp, $data->getCamp());
    }

    public function testImagesConstraintsPresence(): void
    {
        $data = new CampImagesUploadData($this->camp);

        $reflectionClass = new ReflectionClass($data);
        $property = $reflectionClass->getProperty('images');
        $attributes = $property->getAttributes();

        $attributeAll = null;
        $attributeNotBlank = null;

        foreach ($attributes as $attribute)
        {
            if ($attribute->getName() === All::class)
            {
                $attributeAll = $attribute;
            }

            if ($attribute->getName() === NotBlank::class)
            {
                $attributeNotBlank = $attribute;
            }
        }

        $this->assertSame(All::class, $attributeAll->getName());
        $this->assertSame(NotBlank::class, $attributeNotBlank->getName());

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

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
    }
}