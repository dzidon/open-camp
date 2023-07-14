<?php

namespace App\EventDispatcher\EventSubscriber;

use App\Model\Attribute\UpdatedAtProperty;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;

/**
 * Sets the "updated at" date time to entities on update.
 */
#[AsDoctrineListener(event: Events::preUpdate, connection: 'default')]
class DoctrineUpdatedAtSubscriber
{
    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        $reflectionClass = new ReflectionClass($entity);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property)
        {
            $attributes = $property->getAttributes(UpdatedAtProperty::class);

            if (empty($attributes))
            {
                continue;
            }

            /** @var UpdatedAtProperty $attribute */
            $attribute = $attributes[array_key_first($attributes)]->newInstance();
            $dateTimeType = $attribute->getDateTimeType();
            $now = new $dateTimeType('now');

            $property->setValue($entity, $now);
        }
    }
}