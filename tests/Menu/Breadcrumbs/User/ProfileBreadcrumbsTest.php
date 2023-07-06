<?php

namespace App\Tests\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\User\ProfileBreadcrumbs;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileBreadcrumbsTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private MenuTypeFactoryRegistryInterface $menuTypeRegistry;
    private ProfileBreadcrumbs $breadcrumbs;

    public function testBuildBilling(): void
    {
        $breadcrumbsMenu = $this->breadcrumbs->buildBilling();
        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_profile_billing',
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

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
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

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
        ], $this->getGraphNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_profile_password_change_create');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/profile/password-change-create', $profileButton->getUrl());
    }

    protected function setUp(): void
    {
        $this->container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeFactoryRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;

        /** @var ProfileBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get(ProfileBreadcrumbs::class);
        $this->breadcrumbs = $breadcrumbs;
    }
}