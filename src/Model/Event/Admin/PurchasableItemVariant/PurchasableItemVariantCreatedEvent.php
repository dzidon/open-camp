<?php

namespace App\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use LogicException;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasableItemVariantCreatedEvent extends Event
{
    public const NAME = 'model.admin.purchasable_item_variant.created';

    private PurchasableItemVariantCreationData $data;

    private PurchasableItemVariant $entity;

    /** @var PurchasableItemVariantValue[] */
    private array $values;

    public function __construct(PurchasableItemVariantCreationData $data, PurchasableItemVariant $entity, array $values)
    {
        foreach ($values as $value)
        {
            if (!$value instanceof PurchasableItemVariantValue)
            {
                throw new LogicException(
                    sprintf("Values passed to the constructor of %s must all be instances of %s.", self::class, PurchasableItemVariantValue::class)
                );
            }
        }

        $this->data = $data;
        $this->entity = $entity;
        $this->values = $values;
    }

    public function getPurchasableItemVariantCreationData(): PurchasableItemVariantCreationData
    {
        return $this->data;
    }

    public function setPurchasableItemVariantCreationData(PurchasableItemVariantCreationData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPurchasableItemVariant(): PurchasableItemVariant
    {
        return $this->entity;
    }

    public function setPurchasableItemVariant(PurchasableItemVariant $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getPurchasableItemVariantValues(): array
    {
        return $this->values;
    }

    public function addPurchasableItemVariantValue(PurchasableItemVariantValue $value): self
    {
        if (in_array($value, $this->values, true))
        {
            return $this;
        }

        $this->values[] = $value;

        return $this;
    }

    public function removePurchasableItemVariantValue(PurchasableItemVariantValue $value): self
    {
        $key = array_search($value, $this->values, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->values[$key]);

        return $this;
    }
}