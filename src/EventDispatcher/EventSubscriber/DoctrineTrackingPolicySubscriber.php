<?php

namespace App\EventDispatcher\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Switches the Doctrine change tracking policy to deferred explicit.
 */
#[AsDoctrineListener(event: Events::loadClassMetadata, connection: 'default')]
class DoctrineTrackingPolicySubscriber
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();
        $classMetadata->setChangeTrackingPolicy(
            ClassMetadataInfo::CHANGETRACKING_DEFERRED_EXPLICIT
        );
    }
}