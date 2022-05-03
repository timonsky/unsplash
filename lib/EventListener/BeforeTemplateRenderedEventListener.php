<?php
/**
 * This file is part of the Unpslash App
 * and licensed under the AGPL.
 */

namespace OCA\Unsplash\EventListener;

use OCA\Unsplash\Services\SettingsService;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\Util;

class BeforeTemplateRenderedEventListener implements IEventListener {

    /** @var SettingsService */
    protected $settingsService;
    /** @var IRequest */
    protected $request;
    /** @var IURLGenerator */
    private $urlGenerator;

    /**
     * BeforeTemplateRenderedEventListener constructor.
     *
     * @param SettingsService $settingsService
     * @param IRequest        $request
     */
    public function __construct(SettingsService $settingsService, IRequest $request, IURLGenerator $urlGenerator) {
        $this->settingsService = $settingsService;
        $this->request = $request;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Event $event
     */
    public function handle(Event $event): void {
        if(!($event instanceof BeforeTemplateRenderedEvent)) {
            return;
        }

        if($event->isLoggedIn()) {
            $route = $this->request->getParam('_route');
            $userstyleHeader = $this->settingsService->getUserStyleHeaderEnabled();
            $serverstyleHeader = $this->settingsService->getServerStyleHeaderEnabled();
            $serverstyleDash = $this->settingsService->getServerStyleDashboardEnabled();

            if($serverstyleHeader && $userstyleHeader && $route !== 'dashboard.dashboard.index') {
                $this->addHeaderFor('header');
            }

            if($serverstyleDash && $route === 'dashboard.dashboard.index') {
                $this->addHeaderFor('dashboard');
            }
        }

        if(!$event->isLoggedIn() && $this->settingsService->getServerStyleLoginEnabled()) {
            $this->addHeaderFor('login');
        }
    }

    /**
     * Create both links, for static and dynamic css.
     * @param String $target
     * @return void
     */
    private function addHeaderFor(String $target) {
        $linkToCSS = $this->urlGenerator->linkToRoute('unsplash.css.' . $target);

        Util::addHeader('link', [
            'rel' => 'stylesheet',
            'href' => $linkToCSS,
        ]);

        Util::addStyle('unsplash', $target.'_static');
    }

}
