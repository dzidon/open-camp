<?php

namespace App\Form\DataTransfer\Registry;

use App\Form\DataTransfer\Transfer\DataTransferInterface;

/**
 * Central registry that holds all data transfer services.
 */
interface DataTransferRegistryInterface
{
    /**
     * Adds a data transfer service to the registry.
     *
     * @internal
     *
     * @param DataTransferInterface $dataTransfer
     * @return void
     */
    public function registerDataTransfer(DataTransferInterface $dataTransfer): void;

    /**
     * Finds a data transfer service that supports both data and entity objects and fills the data with entity values.
     * Throws a LogicException if no data transfer service is found.
     *
     * @param object $data
     * @param object $entity
     * @return void
     */
    public function fillData(object $data, object $entity): void;

    /**
     * Finds a data transfer service that supports both data and entity objects and fills the entity with the data.
     * Throws a LogicException if no data transfer service is found.
     *
     * @param object $data
     * @param object $entity
     * @return void
     */
    public function fillEntity(object $data, object $entity): void;
}