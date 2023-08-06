<?php

namespace App\Service\Data\Registry;

use App\Service\Data\Transfer\DataTransferInterface;
use LogicException;

/**
 * @inheritDoc
 */
class DataTransferRegistry implements DataTransferRegistryInterface
{
    /**
     * @var DataTransferInterface[]
     */
    private array $dataTransfers = [];

    /**
     * @inheritDoc
     */
    public function registerDataTransfer(DataTransferInterface $dataTransfer): void
    {
        $this->dataTransfers[] = $dataTransfer;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        foreach ($this->dataTransfers as $dataTransfer)
        {
            if ($dataTransfer->supports($data, $entity))
            {
                $dataTransfer->fillData($data, $entity);

                return;
            }
        }

        throw new LogicException(
            sprintf('Method "fillData" cannot be used. There is no Data that supports data "%s" and entity "%s".', $data::class, $entity::class)
        );
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        foreach ($this->dataTransfers as $dataTransfer)
        {
            if ($dataTransfer->supports($data, $entity))
            {
                $dataTransfer->fillEntity($data, $entity);

                return;
            }
        }

        throw new LogicException(
            sprintf('Method "fillEntity" cannot be used. There is no Data that supports data "%s" and entity "%s".', $data::class, $entity::class)
        );
    }
}