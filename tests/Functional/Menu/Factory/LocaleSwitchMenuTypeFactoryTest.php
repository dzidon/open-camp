<?php

namespace App\Tests\Functional\Menu\Factory;

use App\Menu\Factory\LocaleSwitchMenuTypeFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Tests the factory that creates the locale switch menu type.
 */
class LocaleSwitchMenuTypeFactoryTest extends KernelTestCase
{
    /**
     * Tests that the locale switch menu type gets instantiated correctly.
     *
     * @return void
     */
    public function testBuildMenuType(): void
    {
        $factory = $this->createLocaleSwitchMenuTypeFactory();
        $menu = $factory->buildMenuType();

        $this->assertTrue($menu->hasChild('en'));
        $this->assertTrue($menu->hasChild('cs'));
        $this->assertTrue($menu->hasChild('de'));

        $buttonEn = $menu->getChild('en');
        $buttonCs = $menu->getChild('cs');
        $buttonDe = $menu->getChild('de');

        $this->assertFalse($buttonEn->isActive());
        $this->assertTrue($buttonCs->isActive());
        $this->assertFalse($buttonDe->isActive());

        $this->assertSame('English', $buttonEn->getText());
        $this->assertSame('Čeština', $buttonCs->getText());
        $this->assertSame('Deutsch', $buttonDe->getText());

        $this->assertSame('/en/route/mock?get_parameter=value', $buttonEn->getUrl());
        $this->assertSame('/cs/route/mock?get_parameter=value', $buttonCs->getUrl());
        $this->assertSame('/de/route/mock?get_parameter=value', $buttonDe->getUrl());
    }

    /**
     * Instantiates the menu factory.
     *
     * @return LocaleSwitchMenuTypeFactory
     */
    private function createLocaleSwitchMenuTypeFactory(): LocaleSwitchMenuTypeFactory
    {
        self::bootKernel();
        $container = static::getContainer();

        $request = new Request([
            'get_parameter' => 'value',
        ], [], [
            '_route'  => 'locale_route_mock',
            '_locale' => 'cs',
            '_route_params', [
                '_locale' => 'cs'
            ]
        ]);

        $request->setLocale('cs');

        /** @var RequestStack $requestStack */
        $requestStack = $container->get(RequestStack::class);
        $requestStack->push($request);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        $locales = ['en', 'cs', 'de'];
        $localeNames = [
            'en' => 'English',
            'cs' => 'Čeština',
            'de' => 'Deutsch',
        ];

        return new LocaleSwitchMenuTypeFactory($requestStack, $urlGenerator, $locales, $localeNames);
    }
}