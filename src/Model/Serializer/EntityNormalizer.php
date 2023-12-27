<?php

namespace App\Model\Serializer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes Doctrine entities.
 */
class EntityNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private EntityManagerInterface $entityManager;

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $metadata = $this->entityManager->getClassMetadata(get_class($object));
        $result = [];

        foreach ($metadata->getFieldNames() as $fieldName)
        {
            $value = $this->propertyAccessor->getValue($object, $fieldName);
            $result[$fieldName] = $this->normalizer->normalize($value, $format, $context);
        }

        foreach ($metadata->getAssociationNames() as $associationName)
        {
            if ($metadata->isAssociationInverseSide($associationName))
            {
                continue;
            }

            $value = $this->propertyAccessor->getValue($object, $associationName);

            if (is_iterable($value))
            {
                $ids = [];

                foreach ($value as $item)
                {
                    $ids[] = $this->getEntityIdentifierStrings($item);
                }

                $result[$associationName] = $ids;
            }
            else
            {
                if ($value instanceof Proxy || is_subclass_of($value, Proxy::class))
                {
                    continue;
                }

                if ($value === null)
                {
                    $result[$associationName] = null;

                    continue;
                }

                $result[$associationName] = $this->getEntityIdentifierStrings($value);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(mixed $data, string $format = null /* , array $context = [] */): bool
    {
        $class = ClassUtils::getClass($data);

        return !$this->entityManager
            ->getMetadataFactory()
            ->isTransient($class)
        ;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => true,
        ];
    }

    private function getEntityIdentifierStrings(object $entity): array
    {
        $ids = $this->entityManager
            ->getMetadataFactory()
            ->getMetadataFor(get_class($entity))
            ->getIdentifierValues($entity)
        ;

        $idStrings = [];

        foreach ($ids as $id)
        {
            $idStrings[] = (string) $id;
        }

        return $idStrings;
    }
}