<?php

namespace App\Tests\Service\Form\Extension;

use App\Service\Form\Extension\CollectionTypeStimulusExtension;
use App\Tests\Service\Form\Type\CollectionMockType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests that the CollectionType is compatible with Stimulus.
 */
class CollectionTypeStimulusExtensionTest extends TypeTestCase
{
    /**
     * Tests that the CollectionType receives the required attributes.
     *
     * @return void
     */
    public function testAttributes(): void
    {
        $form = $this->factory->create(CollectionMockType::class);
        $collection = $form->get('collection');
        $view = $collection->createView();
        $attributes = $view->vars['attr'];

        $actionsExpected = [
            'fc--add:addItem@window->fc--wrap#addItem',
            'fc--rem-prep:resetPreparedItem@window->fc--wrap#resetPreparedItem',
            'fc--rem-prep:prepareItemForRemoval->fc--wrap#prepareItemForRemoval',
            'fc--rem-mod:removePreparedItem@window->fc--wrap#removePreparedItem',
        ];

        $attributesExpected = [
            'data-controller'                     => 'fc--wrap',
            'data-fc--wrap-target'                => 'fields',
            'data-fc--wrap-collection-name-value' => 'collection',
            'data-fc--wrap-form-name-value'       => 'collection_mock',
            'data-action'                         => implode(' ', $actionsExpected),
        ];

        $this->assertSame($attributesExpected, $attributes);
    }

    /**
     * When the parent type is null, CollectionType cannot be used with Stimulus.
     *
     * @return void
     */
    public function testNullParent(): void
    {
        $form = $this->factory->create(CollectionType::class, null, [
            'entry_type' => TextType::class,
        ]);

        $view = $form->createView();
        $attributes = $view->vars['attr'];
        $this->assertEmpty($attributes);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new CollectionTypeStimulusExtension(),
        ];
    }
}