<?php

namespace App\Model\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Abstraction for all repositories.
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * Saves an entity. Can flush (update the database).
     *
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    protected function save(object $entity, bool $flush): void
    {
        $this->_em->persist($entity);

        if ($flush)
        {
            $this->_em->flush();
        }
    }

    /**
     * Removes an entity. Can flush (update the database).
     *
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    protected function remove(object $entity, bool $flush): void
    {
        $this->_em->remove($entity);

        if ($flush)
        {
            $this->_em->flush();
        }
    }
}