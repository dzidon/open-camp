<?php

namespace App\Tests\Service\Form\Type\Common;

use App\Service\Form\Type\Common\CheckboxWithUrlType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests the checkbox that has a link in its label.
 */
class CheckboxWithUrlTypeTest extends TypeTestCase
{
    /**
     * Tests that the default values are set.
     *
     * @return void
     */
    public function testDefaults(): void
    {
        $form = $this->factory->create(CheckboxWithUrlType::class, null, [
            'checkbox_link_label' => 'Label...'
        ]);
        $config = $form->getConfig();

        $url = $config->getOption('checkbox_link_url');
        $this->assertSame('#', $url);

        $attributes = $config->getOption('checkbox_link_attr');
        $this->assertSame([], $attributes);

        $translationParams = $config->getOption('checkbox_link_translation_parameters');
        $this->assertSame([], $translationParams);

        $label = $config->getOption('checkbox_link_label');
        $this->assertSame('Label...', $label);
    }

    /**
     * Tests the view variables.
     *
     * @return void
     */
    public function testView(): void
    {
        $form = $this->factory->create(CheckboxWithUrlType::class, null, [
            'checkbox_link_label'                  => 'Label...',
            'checkbox_link_url'                    => 'url/url',
            'checkbox_link_attr'                   => ['a' => 'b'],
            'checkbox_link_translation_parameters' => ['x' => 'y'],
        ]);
        $view = $form->createView();

        $this->assertSame('Label...', $view->vars['checkbox_link_label']);
        $this->assertSame('url/url', $view->vars['checkbox_link_url']);
        $this->assertSame(['a' => 'b'], $view->vars['checkbox_link_attr']);
        $this->assertSame(['x' => 'y'], $view->vars['checkbox_link_translation_parameters']);
    }
}