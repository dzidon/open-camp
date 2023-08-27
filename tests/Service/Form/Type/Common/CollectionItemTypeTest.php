<?php

namespace App\Tests\Service\Form\Type\Common;

use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests the base type for collection type items.
 */
class CollectionItemTypeTest extends TypeTestCase
{
    /**
     * Tests that the default values are set.
     *
     * @return void
     */
    public function testDefaults(): void
    {
        $form = $this->factory->create(CollectionItemType::class);
        $config = $form->getConfig();
        $rowAttributes = $config->getOption('row_attr');
        $this->assertSame('item', $rowAttributes['data-fc--wrap-target']);

        $removeButton = $config->getOption('remove_button');
        $this->assertFalse($removeButton);

        $hasButton = $form->has('removeItem');
        $this->assertFalse($hasButton);

        $priority = $config->getOption('remove_button_priority');
        $this->assertSame(-100, $priority);
    }

    /**
     * Tests that the remove button has all required options.
     *
     * @return void
     */
    public function testRemoveButton(): void
    {
        $form = $this->factory->create(CollectionItemType::class, null, [
            'remove_button'          => true,
            'remove_button_priority' => -99,
        ]);

        $hasButton = $form->has('removeItem');
        $this->assertTrue($hasButton);

        $button = $form->get('removeItem');
        $buttonConfig = $button->getConfig();
        $attributes = $buttonConfig->getOption('attr');
        $expectedAttributes = [
            'class'                    => 'btn btn-danger',
            'data-toggle'           => 'modal',
            'data-target'           => '#fc-removal-modal',
            'data-controller'          => 'fc--rem-prep',
            'data-fc--rem-prep-target' => 'button',
            'data-action'              => 'fc--rem-prep#prepareItemForRemoval',
        ];

        $this->assertSame($expectedAttributes, $attributes);

        $priority = $buttonConfig->getOption('priority');
        $this->assertSame(-99, $priority);
    }
}