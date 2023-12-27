<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\TripLocationData;
use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Library\Data\Admin\TripLocationPathData;
use App\Model\Entity\TripLocationPath;
use LogicException;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TripLocationPathCreationDataTest extends KernelTestCase
{
    private TripLocationPath $tripLocationPath;

    public function testTripLocationPathData(): void
    {
        $data = new TripLocationPathCreationData();
        $this->assertInstanceOf(TripLocationPathData::class, $data->getTripLocationPathData());
    }

    public function testTripLocationPathDataValidation(): void
    {
        $validator = $this->getValidator();
        $data = new TripLocationPathCreationData();

        $result = $validator->validateProperty($data, 'tripLocationPathData');
        $this->assertNotEmpty($result); // invalid

        $tripLocationPathData = $data->getTripLocationPathData();
        $tripLocationPathData->setName('Path');
        $result = $validator->validateProperty($data, 'tripLocationPathData');
        $this->assertEmpty($result); // valid
    }

    public function testTripLocationsData(): void
    {
        $data = new TripLocationPathCreationData();
        $this->assertSame([], $data->getTripLocationsData());

        $newTripLocationsData = [
            new TripLocationData(),
            new TripLocationData(),
        ];

        foreach ($newTripLocationsData as $newTripLocationData)
        {
            $data->addTripLocationData($newTripLocationData);
        }

        $this->assertSame($newTripLocationsData, $data->getTripLocationsData());

        $data->removeTripLocationData($newTripLocationsData[0]);
        $this->assertNotContains($newTripLocationsData[0], $data->getTripLocationsData());

        $data = new TripLocationPathCreationData();
        $data->setTripLocationsData($newTripLocationsData);
        $this->assertSame($newTripLocationsData, $data->getTripLocationsData());

        $this->expectException(LogicException::class);
        $newTripLocationsData = [new stdClass()];
        $data->setTripLocationsData($newTripLocationsData);
    }

    public function testTripLocationsDataValidation(): void
    {
        $data = new TripLocationPathCreationData();
        $validator = $this->getValidator();

        $result = $validator->validateProperty($data, 'tripLocationsData');
        $this->assertNotEmpty($result); // invalid

        $tripLocationData = new TripLocationData();
        $tripLocationData->setName('Location');
        $tripLocationData->setPrice(100.0);
        $tripLocationData->setPriority(0);
        $data->addTripLocationData($tripLocationData);

        $result = $validator->validateProperty($data, 'tripLocationsData');
        $this->assertEmpty($result); // valid
    }

    public function testTripLocationUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $creationData = new TripLocationPathCreationData();
        $pathData = $creationData->getTripLocationPathData();
        $pathData->setName('Path');

        $locationData1 = new TripLocationData(null, $this->tripLocationPath);
        $locationData1->setName('Location 1');
        $locationData1->setPrice(1000.0);
        $locationData1->setPriority(100);

        $locationData2 = new TripLocationData(null, $this->tripLocationPath);
        $locationData2->setName('Location 2');
        $locationData2->setPrice(2000.0);
        $locationData2->setPriority(200);

        $creationData
            ->addTripLocationData($locationData1)
            ->addTripLocationData($locationData2)
        ;

        $result = $validator->validate($creationData);
        $this->assertEmpty($result); // valid

        $locationData2->setName('Location 1');
        $result = $validator->validate($creationData);
        $this->assertNotEmpty($result); // invalid
    }

    protected function setUp(): void
    {
        $this->tripLocationPath = new TripLocationPath('Path');
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}