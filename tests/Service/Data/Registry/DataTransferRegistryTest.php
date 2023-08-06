<?php

namespace App\Tests\Service\Data\Registry;

use App\Service\Data\Registry\DataTransferRegistry;
use App\Tests\Library\Data\LabelDataMock;
use App\Tests\Library\Data\NameDataMock;
use App\Tests\Model\Entity\EntityMock;
use App\Tests\Service\Data\Transfer\LabelDataTransferMock;
use App\Tests\Service\Data\Transfer\NameDataTransferMock;
use PHPUnit\Framework\TestCase;

class DataTransferRegistryTest extends TestCase
{
    private DataTransferRegistry $dataTransferRegistry;

    public function testName(): void
    {
        $entityMock = new EntityMock('a', 'b');
        $nameData = new NameDataMock();

        $this->dataTransferRegistry->fillData($nameData, $entityMock);
        $this->assertSame('a', $nameData->getName());

        $nameData->setName('xyz');
        $this->dataTransferRegistry->fillEntity($nameData, $entityMock);
        $this->assertSame('xyz', $entityMock->getName());
    }

    public function testLabel(): void
    {
        $entityMock = new EntityMock('a', 'b');
        $labelData = new LabelDataMock();

        $this->dataTransferRegistry->fillData($labelData, $entityMock);
        $this->assertSame('b', $labelData->getLabel());

        $labelData->setLabel('321');
        $this->dataTransferRegistry->fillEntity($labelData, $entityMock);
        $this->assertSame('321', $entityMock->getLabel());
    }

    protected function setUp(): void
    {
        $this->dataTransferRegistry = new DataTransferRegistry();

        $this->dataTransferRegistry->registerDataTransfer(new LabelDataTransferMock());
        $this->dataTransferRegistry->registerDataTransfer(new NameDataTransferMock());
    }
}