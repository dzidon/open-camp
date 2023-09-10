<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Service\Menu\Breadcrumbs\User\ProfileBreadcrumbs;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $menuTypeRegistry;
    private ProfileBreadcrumbs $breadcrumbs;

    public function testBuildBilling(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildBilling();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_profile_billing',
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_profile_billing');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/profile/billing', $profileButton->getUrl());
    }

    public function testBuildPasswordChange(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildPasswordChange();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_profile_password_change',
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_profile_password_change');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/profile/password-change', $profileButton->getUrl());
    }

    public function testBuildPasswordChangeCreate(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildPasswordChangeCreate();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_profile_password_change_create',
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_profile_password_change_create');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/profile/password-change-create', $profileButton->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;

        /** @var ProfileBreadcrumbs $breadcrumbs */
        $breadcrumbs = $container->get(ProfileBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}