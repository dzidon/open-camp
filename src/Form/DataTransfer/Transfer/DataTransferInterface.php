<?php

namespace App\Form\DataTransfer\Transfer;

/**
 * Transfers data from a DTO to an entity and vice versa.
 */
interface DataTransferInterface
{
    /**
     * Returns true if the given data and entity objects are supported.
     *
     * @param object $data
     * @param object $entity
     * @return bool
     */
    public function supports(object $data, object $entity): bool;

    /**
     * Fills the DTO with values from the entity.
     *
     * @param object $data
     * @param object $entity
     * @return void
     */
    public function fillData(object $data, object $entity): void;

    /**
     * Fills the entity with values from the DTO.
     *
     * @param object $data
     * @param object $entity
     * @return void
     */
    public function fillEntity(object $data, object $entity): void;
}