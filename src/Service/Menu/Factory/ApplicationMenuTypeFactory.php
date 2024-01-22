<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the application navbar.
 */
class ApplicationMenuTypeFactory extends AbstractMenuTypeFactory
{
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(TranslatorInterface   $translator,
                                UrlGeneratorInterface $urlGenerator,
                                RequestStack          $requestStack)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'application';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        /** @var Application|CampDate $source */
        $source = $options['source'];
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        // root

        $menu = new MenuType(self::getMenuIdentifier(), 'application');

        // step one - create

        if ($source instanceof CampDate)
        {
            $campDateIdId = $source
                ->getId()
                ->toRfc4122()
            ;

            $text = '1.) ' . $this->translator->trans('route.user_application_step_one_create');
            $url = $this->urlGenerator->generate('user_application_step_one_create', ['campDateId' => $campDateIdId]);
            $itemStepOneCreate = new MenuType('user_application_step_one_create', 'application_item', $text, $url);
            $menu->addChild($itemStepOneCreate);
            $itemStepOneCreate->setActive();

            $text = '2.) ' . $this->translator->trans('route.user_application_step_two');
            $itemStepTwo = new MenuType('user_application_step_two', 'application_item', $text);
            $menu->addChild($itemStepTwo);

            $text = '3.) ' . $this->translator->trans('route.user_application_step_three');
            $itemStepThree = new MenuType('user_application_step_three', 'application_item', $text);
            $menu->addChild($itemStepThree);
        }
        else
        {
            $applicationId = $source
                ->getId()
                ->toRfc4122()
            ;

            $text = '1.) ' . $this->translator->trans('route.user_application_step_one_update');
            $url = $this->urlGenerator->generate('user_application_step_one_update', ['applicationId' => $applicationId]);
            $itemStepOneCreate = new MenuType('user_application_step_one_update', 'application_item', $text, $url);
            $menu->addChild($itemStepOneCreate);
            $itemStepOneCreate->setActive($route === 'user_application_step_one_update');

            $text = '2.) ' . $this->translator->trans('route.user_application_step_two');
            $url = $this->urlGenerator->generate('user_application_step_two', ['applicationId' => $applicationId]);
            $itemStepTwo = new MenuType('user_application_step_two', 'application_item', $text);
            $menu->addChild($itemStepTwo);
            $itemStepTwo->setActive($route === 'user_application_step_two');

            if ($route === 'user_application_step_two' || $route === 'user_application_step_three')
            {
                $itemStepTwo->setUrl($url);
            }

            $text = '3.) ' . $this->translator->trans('route.user_application_step_three');
            $url = $this->urlGenerator->generate('user_application_step_three', ['applicationId' => $applicationId]);
            $itemStepThree = new MenuType('user_application_step_three', 'application_item', $text);
            $menu->addChild($itemStepThree);
            $itemStepThree->setActive($route === 'user_application_step_three');

            if ($route === 'user_application_step_three')
            {
                $itemStepThree->setUrl($url);
            }
        }

        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('source');
        $resolver->setAllowedTypes('source', [Application::class, CampDate::class]);
    }
}