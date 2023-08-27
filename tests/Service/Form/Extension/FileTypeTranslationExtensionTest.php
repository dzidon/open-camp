<?php

namespace App\Tests\Service\Form\Extension;

use App\Service\Form\Extension\FileTypeTranslationExtension;
use App\Tests\Service\Translation\TranslatorMock;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Test\TypeTestCase;

class FileTypeTranslationExtensionTest extends TypeTestCase
{
    public function testView(): void
    {
        $form = $this->factory->create(FileType::class);
        $view = $form->createView();

        $this->assertArrayHasKey('label_attr', $view->vars);

        $labelAttr = $view->vars['label_attr'];
        $this->assertArrayHasKey('data-browse', $labelAttr);

        $dataBrowse = $labelAttr['data-browse'];
        $this->assertSame('form.common.file.browse', $dataBrowse);
    }

    protected function getTypeExtensions(): array
    {
        $translatorMock = new TranslatorMock();

        return [
            new FileTypeTranslationExtension($translatorMock),
        ];
    }
}