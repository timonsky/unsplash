<?php
/**
 * This file is part of the Unsplash App
 * and licensed under the AGPL.
 */

namespace OCA\Unsplash\Settings;

use OC_Defaults;
use OCA\Unsplash\Services\SettingsService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IURLGenerator;
use OCP\Settings\ISettings;

/**
 * Class PersonalSettingsController
 *
 * @package OCA\Unsplash\Controller\Settings
 */
class PersonalSettings implements ISettings {

    /**
     * @var IURLGenerator
     */
    protected $urlGenerator;

    /**
     * @var SettingsService
     */
    protected $settings;

    /**
     * @var OC_Defaults
     */
    protected $theming;

    /**
     * @var string
     */
    protected $appName;

    /**
     * AdminSection constructor.
     *
     * @param IURLGenerator   $urlGenerator
     * @param SettingsService $settings
     * @param OC_Defaults     $theming
     * @param                 $appName
     */
    public function __construct(IURLGenerator $urlGenerator, SettingsService $settings, OC_Defaults $theming, $appName) {
        $this->urlGenerator = $urlGenerator;
        $this->settings     = $settings;
        $this->theming      = $theming;
        $this->appName      = $appName;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return TemplateResponse
     */
    public function getForm() {
        return new TemplateResponse('unsplash', 'settings/personal', [
            'saveSettingsUrl' => $this->urlGenerator->linkToRouteAbsolute('unsplash.personal_settings.set'),
            'styleHeader'     => $this->settings->getUserStyleHeaderEnabled(),
            'hasDashboard'    => $this->settings->getNextcloudVersion() > 19,
            'selectedProvider'=> str_replace(' ', '', $this->settings->getImageProviderName()),
            'enableNavbar'    => $this->settings->getServerStyleHeaderEnabled(),
            'label'           => $this->theming->getEntity()
        ], '');
    }

    /**
     * @return string
     */
    public function getSection() {
        return $this->appName;
    }

    /**
     * @return int
     */
    public function getPriority(): int {
        return 75;
    }
}
