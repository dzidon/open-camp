<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Adds functionality to Symfony's abstract controller.
 */
abstract class AbstractController extends SymfonyAbstractController
{
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['translator'] = '?'.TranslatorInterface::class;

        return $services;
    }

    /**
     * Translates a message.
     *
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    protected function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $this->assertTranslatorService();

        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        return $translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Translates a message and adds it to the flash bag.
     *
     * @param string $type
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return void
     */
    protected function addTransFlash(string $type, string $id, array $parameters = [], string $domain = null, string $locale = null): void
    {
        $message = $this->trans($id, $parameters, $domain, $locale);
        $this->addFlash($type, $message);
    }

    /**
     * Throws a LogicException if there is no translator service.
     *
     * @return void
     */
    private function assertTranslatorService(): void
    {
        if (!$this->container->has('translator'))
        {
            throw new LogicException('Translating messages in controllers is unavailable, because there is no translator service.');
        }
    }
}