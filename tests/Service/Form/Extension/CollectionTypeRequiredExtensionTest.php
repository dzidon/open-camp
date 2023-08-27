<?php

namespace App\Tests\Service\Form\Extension;

use App\Service\Form\Extension\CollectionTypeRequiredExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

class CollectionTypeRequiredExtensionTest extends TypeTestCase
{
    public function testDefaults(): void
    {
        $form = $this->factory->create(CollectionType::class, null, [
            'entry_type' => TextType::class,
        ]);
        $config = $form->getConfig();

        $suppressRequired = $config->getOption('suppress_required_rendering');
        $this->assertFalse($suppressRequired);
    }

    public function testView(): void
    {
        $form = $this->factory->create(CollectionType::class, null, [
            'entry_type'                  => TextType::class,
            'suppress_required_rendering' => true,
        ]);
        $view = $form->createView();

        $this->assertArrayHasKey('suppress_required_rendering', $view->vars);
        $this->assertTrue($view->vars['suppress_required_rendering']);
    }

    protected function getTypeExtensions(): array
    {
        return [
            new CollectionTypeRequiredExtension(),
        ];
    }
}