<?php

namespace App\Model\EventSubscriber\Common;

use App\Service\Logger\ModelLoggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Logs all model changes.
 */
#[AsDoctrineListener(event: Events::prePersist, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 200, connection: 'default')]
#[AsDoctrineListener(event: Events::preRemove, connection: 'default')]
class DoctrineEntityLogSubscriber
{
    private ModelLoggerInterface $logger;

    public function __construct(ModelLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityName = $this->getEntityName($entity);
        $this->logger->info(sprintf('Created %s.', $entityName), [
            'entity' => $entity,
        ]);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityName = $this->getEntityName($entity);
        $changeSet = $args->getEntityChangeSet();
        $this->logger->info(sprintf('Updated %s.', $entityName), [
            'changeSet' => $changeSet,
        ]);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityName = $this->getEntityName($entity);
        $this->logger->info(sprintf('Deleted %s.', $entityName), [
            'entity' => $entity,
        ]);
    }

    /**
     * Gets entity name without its namespace.
     *
     * @param object $entity
     * @return string
     */
    private function getEntityName(object $entity): string
    {
        $entityClass = get_class($entity);
        $entityNamespaceParts = explode('\\', $entityClass);

        return end($entityNamespaceParts);
    }
}