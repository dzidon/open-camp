<?php

namespace App\Tests\Form\Type;

use App\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Simple form mock with a collection and a button to add new items.
 */
class CollectionMockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('collection', CollectionType::class, [
                'entry_type' => TextType::class,
            ])
            ->add('addItem', CollectionAddItemButtonType::class, [
                'collection_name' => 'collection',
            ])
        ;
    }
}