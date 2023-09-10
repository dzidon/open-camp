<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\TripLocationData;
use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Library\Data\Admin\TripLocationPathData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TripLocationPathCreationDataTest extends KernelTestCase
{
    public function testTripLocationPathData(): void
    {
        $data = new TripLocationPathCreationData();
        $this->assertInstanceOf(TripLocationPathData::class, $data->getTripLocationPathData());
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
            $data->addTripLocationsDatum($newTripLocationData);
        }

        $this->assertSame($newTripLocationsData, $data->getTripLocationsData());

        $data->removeTripLocationsDatum($newTripLocationsData[0]);
        $this->assertNotContains($newTripLocationsData[0], $data->getTripLocationsData());
    }

    public function testTripLocationUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $creationData = new TripLocationPathCreationData();
        $pathData = $creationData->getTripLocationPathData();
        $pathData->setName('Path');

        $locationData1 = new TripLocationData();
        $locationData1->setName('Location 1');
        $locationData1->setPrice(1000.0);
        $locationData1->setPriority(100);

        $locationData2 = new TripLocationData();
        $locationData2->setName('Location 2');
        $locationData2->setPrice(2000.0);
        $locationData2->setPriority(200);

        $creationData
            ->addTripLocationsDatum($locationData1)
            ->addTripLocationsDatum($locationData2)
        ;

        $result = $validator->validate($creationData);
        $this->assertEmpty($result); // valid

        $locationData2->setName('Location 1');
        $result = $validator->validate($creationData);
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