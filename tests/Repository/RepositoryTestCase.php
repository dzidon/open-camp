<?php

namespace App\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Abstraction for repository tests.
 */
abstract class RepositoryTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $container = static::getContainer();

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager()
        ;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}