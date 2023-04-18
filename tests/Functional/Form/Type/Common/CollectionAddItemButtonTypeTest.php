<?php

namespace App\Tests\Functional\Form\Type\Common;

use App\Form\Type\Common\CollectionAddItemButtonType;
use App\Tests\Functional\Form\Type\CollectionMockType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests the button that adds items to CollectionType using Stimulus.
 */
class CollectionAddItemButtonTypeTest extends TypeTestCase
{
    /**
     * Tests that the button receives the required attributes.
     *
     * @return void
     */
    public function testAttributes(): void
    {
        $form = $this->factory->create(CollectionMockType::class);
        $collection = $form->get('addItem');
        $view = $collection->createView();
        $attributes = $view->vars['attr'];

        $attributesExpected = [
            'data-controller'                    => 'fc--add',
            'data-action'                        => 'fc--add#addItem',
            'data-fc--add-collection-name-value' => 'collection',
            'data-fc--add-form-name-value'       => 'collection_mock',
        ];

        $this->assertSame($attributesExpected, $attributes);
    }

    /**
     * When the parent type is null, the button cannot be used with Stimulus.
     *
     * @return void
     */
    public function testNullParent(): void
    {
        $form = $this->factory->create(CollectionAddItemButtonType::class, null, [
            'collection_name' => 'collection'
        ]);

        $view = $form->createView();
        $attributes = $view->vars['attr'];
        $this->assertEmpty($attributes);
    }
}