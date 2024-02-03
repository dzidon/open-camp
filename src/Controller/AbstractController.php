<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * Creates an exception that can be thrown to redirect to the given url.
     *
     * @param string $url
     * @param int $status
     * @return HttpException
     */
    public function createRedirectException(string $url, int $status = 302): HttpException
    {
        $redirectResponse = $this->redirect($url, $status);
        $statusCode = $redirectResponse->getStatusCode();
        $headers = $redirectResponse->headers->all();

        return new HttpException($statusCode, '', null, $headers);
    }

    /**
     * Creates an exception that can be thrown to redirect to the given route.
     *
     * @param string $route
     * @param array $parameters
     * @param int $status
     * @return HttpException
     */
    public function createRedirectToRouteException(string $route, array $parameters = [], int $status = 302): HttpException
    {
        $redirectResponse = $this->redirectToRoute($route, $parameters, $status);
        $url = $redirectResponse->getTargetUrl();
        $status = $redirectResponse->getStatusCode();

        return $this->createRedirectException($url, $status);
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