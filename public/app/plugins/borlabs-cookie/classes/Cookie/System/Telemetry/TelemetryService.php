<?php
/*
 *  Copyright (c) 2024 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\Telemetry;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\TelemetryApiClient;
use Borlabs\Cookie\Dto\Telemetry\ContentBlockerDto;
use Borlabs\Cookie\Dto\Telemetry\PackageDto;
use Borlabs\Cookie\Dto\Telemetry\PluginDto;
use Borlabs\Cookie\Dto\Telemetry\ServiceDto;
use Borlabs\Cookie\Dto\Telemetry\SettingsDto;
use Borlabs\Cookie\Dto\Telemetry\SystemInformationDto;
use Borlabs\Cookie\Dto\Telemetry\TelemetryDto;
use Borlabs\Cookie\Dto\Telemetry\ThemeDto;
use Borlabs\Cookie\DtoList\Telemetry\ContentBlockerDtoList;
use Borlabs\Cookie\DtoList\Telemetry\PackageDtoList;
use Borlabs\Cookie\DtoList\Telemetry\PluginDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ServiceDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ThemeDtoList;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;

class TelemetryService
{
    private ContentBlockerRepository $contentBlockerRepository;

    private DialogSettingsConfig $dialogSettingsConfig;

    private IabTcfConfig $iabTcfConfig;

    private Language $language;

    private Option $option;

    private PackageRepository $packageRepository;

    private PluginConfig $pluginConfig;

    private ServiceRepository $serviceRepository;

    private TelemetryApiClient $telemetryApiClient;

    private WidgetConfig $widgetConfig;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerRepository $contentBlockerRepository,
        DialogSettingsConfig $dialogSettingsConfig,
        IabTcfConfig $iabTcfConfig,
        Language $language,
        Option $option,
        PackageRepository $packageRepository,
        PluginConfig $pluginConfig,
        ServiceRepository $serviceRepository,
        TelemetryApiClient $telemetryApiClient,
        WidgetConfig $widgetConfig,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->language = $language;
        $this->option = $option;
        $this->packageRepository = $packageRepository;
        $this->pluginConfig = $pluginConfig;
        $this->serviceRepository = $serviceRepository;
        $this->telemetryApiClient = $telemetryApiClient;
        $this->widgetConfig = $widgetConfig;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function getInstalledPackages(): PackageDtoList
    {
        $installedPackages = $this->packageRepository->getInstalledPackages();
        $packages = new PackageDtoList();

        foreach ($installedPackages as $packageModel) {
            $packageDto = new PackageDto();
            $packageDto->key = $packageModel->borlabsServicePackageKey;
            $packageDto->name = $packageModel->name;
            $packageDto->version = $packageModel->version->major . '.' . $packageModel->version->minor . '.' . $packageModel->version->patch;
            $packages->add($packageDto);
        }

        return $packages;
    }

    public function getTelemetry(): ?TelemetryDto
    {
        $telemetryDto = new TelemetryDto();
        // The following data is crucial for the development of the plugin and the maintenance of Borlabs Cookie packages.
        $telemetryDto->borlabsCookiePackages = $this->getInstalledPackages();
        $telemetryDto->systemInformation = $this->getSystemInformation();

        if ((bool) $this->option->getGlobal('TelemetryStatus')->value === false) {
            // To monitor compliance with the TCF guidelines of the IAB
            if ($this->iabTcfConfig->get()->iabTcfStatus === true) {
                $telemetryDto->borlabsCookieSettings = $this->getSettings();
            } elseif (count($telemetryDto->borlabsCookiePackages->list) === 0) {
                // There are no telemetry data that could be sent
                return null;
            }

            return $telemetryDto;
        }

        // This data is optional and is only sent if consent to telemetry data collection has been given.
        $telemetryDto->borlabsCookieContentBlockers = $this->getContentBlockers();
        $telemetryDto->borlabsCookieServices = $this->getServices();
        $telemetryDto->borlabsCookieSettings = $this->getSettings();
        $telemetryDto->plugins = $this->getPlugins();
        $telemetryDto->themes = $this->getThemes();

        return $telemetryDto;
    }

    public function sendTelemetryData()
    {
        $telemetryDto = $this->getTelemetry();

        if (is_null($telemetryDto)) {
            return;
        }

        $this->telemetryApiClient->sendTelemetryData($telemetryDto);
    }

    private function getContentBlockers(): ContentBlockerDtoList
    {
        $contentBlockerModels = $this->contentBlockerRepository->getAllOfSelectedLanguage();
        $contentBlockers = new ContentBlockerDtoList();

        foreach ($contentBlockerModels as $contentBlockerModel) {
            $contentBlockerDto = new ContentBlockerDto();
            $contentBlockerDto->key = $contentBlockerModel->key;
            $contentBlockerDto->name = $contentBlockerModel->name;
            $contentBlockers->add($contentBlockerDto);
        }

        return $contentBlockers;
    }

    private function getPlugins(): PluginDtoList
    {
        $activePlugins = $this->option->getThirdPartyOption('active_plugins')->value;
        $installedPlugins = $this->wpFunction->getPlugins();
        $plugins = new PluginDtoList();

        foreach ($installedPlugins as $slug => $plugin) {
            $pluginDto = new PluginDto();
            $pluginDto->author = $plugin['Author'];
            $pluginDto->isEnabled = in_array($slug, $activePlugins, true);
            $pluginDto->name = $plugin['Name'];
            $pluginDto->pluginUrl = $plugin['PluginURI'];
            $pluginDto->slug = $slug;
            $pluginDto->textDomain = $plugin['TextDomain'];
            $pluginDto->version = $plugin['Version'];
            $plugins->add($pluginDto);
        }

        return $plugins;
    }

    private function getServices(): ServiceDtoList
    {
        $serviceModels = $this->serviceRepository->getAllOfSelectedLanguage(true);
        $services = new ServiceDtoList();

        foreach ($serviceModels as $serviceModel) {
            $serviceDto = new ServiceDto();
            $serviceDto->key = $serviceModel->key;
            $serviceDto->name = $serviceModel->name;
            $serviceDto->providerName = $serviceModel->provider->name;
            $serviceDto->providerPrivacyUrl = $serviceModel->provider->privacyUrl;
            $services->add($serviceDto);
        }

        return $services;
    }

    private function getSettings(): SettingsDto
    {
        $settingsDto = new SettingsDto();
        $settingsDto->geoIpActive = $this->dialogSettingsConfig->get()->geoIpActive;
        $settingsDto->iabTcfStatus = $this->iabTcfConfig->get()->iabTcfStatus;
        $settingsDto->layout = $this->dialogSettingsConfig->get()->layout;
        $settingsDto->position = $this->dialogSettingsConfig->get()->position;
        $settingsDto->showAcceptAllButton = $this->dialogSettingsConfig->get()->showAcceptAllButton;
        $settingsDto->showAcceptOnlyEssentialButton = $this->dialogSettingsConfig->get()->showAcceptOnlyEssentialButton;
        $settingsDto->showSaveButton = $this->dialogSettingsConfig->get()->showSaveButton;
        $settingsDto->showWidget = $this->widgetConfig->get()->show;

        return $settingsDto;
    }

    private function getSystemInformation(): SystemInformationDto
    {
        $systemInformationDto = new SystemInformationDto();
        $systemInformationDto->databaseVersion = Database::getDbVersion();
        $systemInformationDto->phpVersion = phpversion();
        $systemInformationDto->product = 'borlabs-cookie';
        $systemInformationDto->productAutomaticUpdate = $this->pluginConfig->get()->automaticUpdate ?? AutomaticUpdateEnum::AUTO_UPDATE_ALL();
        $systemInformationDto->productDebugLoggingEnabled = $this->pluginConfig->get()->enableDebugLogging;
        $systemInformationDto->productVersion = BORLABS_COOKIE_VERSION;
        $systemInformationDto->wordPressDatabasePrefix = $this->wpdb->prefix;
        $systemInformationDto->wordPressDefaultLanguage = $this->language->getDefaultLanguage();
        $systemInformationDto->wordPressHasMultilanguagePluginActive = $this->language->isMultilanguagePluginActive();
        $systemInformationDto->wordPressIsMultisite = defined('MULTISITE') && constant('MULTISITE') === true;
        $systemInformationDto->wordPressIsMultisiteSubdomainInstall = defined('SUBDOMAIN_INSTALL') && constant('SUBDOMAIN_INSTALL') === true;
        $systemInformationDto->wordPressMultilanguagePluginLanguages = $this->language->getLanguageList();
        $systemInformationDto->wordPressMultisiteMainSite = defined('DOMAIN_CURRENT_SITE') && defined('PATH_CURRENT_SITE') ? constant('DOMAIN_CURRENT_SITE') . constant('PATH_CURRENT_SITE') : null;
        $systemInformationDto->wordPressVersion = $this->wpFunction->getBlogInfo('version');

        return $systemInformationDto;
    }

    private function getThemes(): ThemeDtoList
    {
        $activeTheme = $this->wpFunction->getWpTheme();
        $installedThemes = $this->wpFunction->getWpThemes();
        $themes = new ThemeDtoList();

        foreach ($installedThemes as $theme) {
            $themeDto = new ThemeDto();
            $themeDto->author = $theme->get('Author');
            $themeDto->isChildtheme = strlen((string) $theme->get('Template')) ? true : false;
            $themeDto->isEnabled = $activeTheme->get_template() === $theme->get_template();
            $themeDto->name = $theme->get('Name');
            $themeDto->template = $theme->get_template();
            $themeDto->textDomain = $theme->get('TextDomain');
            $themeDto->themeUrl = $theme->get('ThemeURI');
            $themeDto->version = $theme->get('Version');
            $themes->add($themeDto);
        }

        return $themes;
    }
}
