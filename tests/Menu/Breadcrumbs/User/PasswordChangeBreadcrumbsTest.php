<?php

namespace App\Tests\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\User\PasswordChangeBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PasswordChangeBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $menuTypeRegistry;
    private PasswordChangeBreadcrumbs $breadcrumbs;

    public function testBuildPasswordChange(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildPasswordChange();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_login',
            'user_password_change'
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_login');
        $this->assertSame(false, $profileButton->isActive());
        $this->assertSame('/login', $profileButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $this->assertSame('/password-change', $userPasswordChangeButton->getUrl());
    }

    public function testBuildPasswordChangeComplete(): void
    {
        $token = 'xyz';
        $breadcrumbsMenu = $this->breadcrumbs->buildPasswordChangeComplete($token);
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_login',
            'user_password_change',
            'user_password_change_complete'
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_login');
        $this->assertSame(false, $profileButton->isActive());
        $this->assertSame('/login', $profileButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change');
        $this->assertSame(false, $userPasswordChangeButton->isActive());
        $this->assertSame('/password-change', $userPasswordChangeButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change_complete');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $expectedUrl = sprintf('/password-change/complete/%s', $token);
        $this->assertSame($expectedUrl, $userPasswordChangeButton->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;

        /** @var PasswordChangeBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(PasswordChangeBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}