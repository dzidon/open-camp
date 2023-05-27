<?php

namespace App\Tests\Menu\Factory;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuType;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Menu type factory used for testing.
 */
class MenuTypeFactoryMock implements MenuTypeFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'menu_mock';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuTypeInterface
    {
        $root = new MenuType(self::getMenuIdentifier(), 'test_root');
        $button = new MenuType('button', 'test_item', $options['button_text']);
        $button->setParent($root);

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('button_text', 'Click');
        $resolver->setAllowedTypes('button_text', 'string');
        $resolver->setRequired(['button_text']);
    }
}