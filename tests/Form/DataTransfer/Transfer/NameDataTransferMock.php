<?php

namespace App\Tests\Form\DataTransfer\Transfer;

use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Tests\Entity\EntityMock;
use App\Tests\Form\DataTransfer\Data\NameDataMock;

class NameDataTransferMock implements DataTransferInterface
{
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof NameDataMock && $entity instanceof EntityMock;
    }

    public function fillData(object $data, object $entity): void
    {
        /** @var NameDataMock $nameData */
        /** @var EntityMock $entityMock */
        $nameData = $data;
        $entityMock = $entity;

        $nameData->setName($entityMock->getName());
    }

    public function fillEntity(object $data, object $entity): void
    {
        /** @var NameDataMock $nameData */
        /** @var EntityMock $entityMock */
        $nameData = $data;
        $entityMock = $entity;

        $entityMock->setName($nameData->getName());
    }
}