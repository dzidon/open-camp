<?php

namespace App\Tests\Menu\Type;

use App\Menu\Type\MenuIconType;
use PHPUnit\Framework\TestCase;

/**
 * Tests a menu type that displays icons.
 */
class MenuIconTypeTest extends TestCase
{
    /**
     * Tests setting and getting the icon.
     *
     * @return void
     */
    public function testIcon(): void
    {
        $item = new MenuIconType('identifier', 'block');
        $this->assertNull($item->getIcon());

        $item->setIcon('icon_name');
        $this->assertSame('icon_name', $item->getIcon());
    }
}