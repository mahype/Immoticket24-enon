<?php
/*
 *  Copyright (c) 2023 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\Script;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\RepositoryQueryBuilderWithRelations;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Converter;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\FileSystem\GlobalStorageFolder;
use Borlabs\Cookie\System\Option\Option;

final class ScriptConfigBuilder
{
    private ContentBlockerRepository $contentBlockerRepository;

    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private DialogLocalization $dialogLocalization;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private FileManager $fileManager;

    private GeneralConfig $generalConfig;

    private GlobalStorageFolder $globalStorageFolder;

    private IabTcfConfig $iabTcfConfig;

    private Option $option;

    private ProviderRepository $providerRepository;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    private WidgetConfig $widgetConfig;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerRepository $contentBlockerRepository,
        DefaultLocalizationStrings $defaultLocalizationStrings,
        DialogLocalization $dialogLocalization,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        FileManager $fileManager,
        GeneralConfig $generalConfig,
        GlobalStorageFolder $globalStorageFolder,
        IabTcfConfig $iabTcfConfig,
        Option $option,
        ProviderRepository $providerRepository,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository,
        WidgetConfig $widgetConfig,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->dialogLocalization = $dialogLocalization;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->fileManager = $fileManager;
        $this->generalConfig = $generalConfig;
        $this->globalStorageFolder = $globalStorageFolder;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->option = $option;
        $this->providerRepository = $providerRepository;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
        $this->widgetConfig = $widgetConfig;
        $this->wpFunction = $wpFunction;
    }

    public function generateConfigFile(string $languageCode)
    {
        // Privacy Policy Link
        $dialogPrivacyLink = '';

        if (!empty($this->dialogSettingsConfig->get()->privacyPageUrl)) {
            $dialogPrivacyLink = $this->dialogSettingsConfig->get()->privacyPageUrl;
        }

        if (!empty($this->dialogSettingsConfig->get()->privacyPageCustomUrl)) {
            $dialogPrivacyLink = $this->dialogSettingsConfig->get()->privacyPageCustomUrl;
        }

        // Imprint Link
        $dialogImprintLink = '';

        if (!empty($this->dialogSettingsConfig->get()->imprintPageUrl)) {
            $dialogImprintLink = $this->dialogSettingsConfig->get()->imprintPageUrl;
        }

        if (!empty($this->dialogSettingsConfig->get()->imprintPageCustomUrl)) {
            $dialogImprintLink = $this->dialogSettingsConfig->get()->imprintPageCustomUrl;
        }

        $brightBackground = false;
        $bgColorHSL = Converter::hexToHsl($this->dialogStyleConfig->get()->dialogBackgroundColor);

        if (isset($bgColorHSL[2]) && $bgColorHSL[2] <= 50) {
            $brightBackground = true;
        }

        // Support Borlabs Cookie
        $supportBorlabsCookie = $this->dialogSettingsConfig->get()->showBorlabsCookieBranding;
        $supportBorlabsCookieLogo = '';

        if ($supportBorlabsCookie) {
            if ($brightBackground) {
                $supportBorlabsCookieLogo = BORLABS_COOKIE_PLUGIN_URL . '/assets/images/borlabs-cookie-icon-white.svg';
            } else {
                $supportBorlabsCookieLogo = BORLABS_COOKIE_PLUGIN_URL . '/assets/images/borlabs-cookie-icon-black.svg';
            }
        }

        // Logo
        $dialogLogo = $this->dialogSettingsConfig->get()->logo;
        $dialogLogoHd = $this->dialogSettingsConfig->get()->logoHd;
        $dialogLogoSrcSet = [];
        $dialogLogoSrcSet[] = $dialogLogo;

        if (!empty($dialogLogoHd)) {
            $dialogLogoSrcSet[] = $dialogLogoHd . ' 2x';
        }

        $settings = [
            'automaticCookieDomainAndPath' => $this->generalConfig->get()->automaticCookieDomainAndPath,
            'cookieCrossCookieDomains' => $this->generalConfig->get()->crossCookieDomains,
            'cookieDomain' => $this->generalConfig->get()->cookieDomain,
            'cookieLifetime' => $this->generalConfig->get()->cookieLifetime,
            'cookieLifetimeEssentialOnly' => $this->generalConfig->get()->cookieLifetimeEssentialOnly,
            'cookiePath' => $this->generalConfig->get()->cookiePath,
            'cookieSameSite' => (string) $this->generalConfig->get()->cookieSameSite,
            'cookieSecure' => $this->generalConfig->get()->cookieSecure,
            'cookieVersion' => (int) $this->option->getGlobal('CookieVersion', 1)->value,
            'cookiesForBots' => $this->generalConfig->get()->cookiesForBots,

            'dialogAnimation' => $this->dialogSettingsConfig->get()->animation,
            'dialogAnimationDelay' => $this->dialogSettingsConfig->get()->animationDelay,
            'dialogAnimationIn' => $this->dialogSettingsConfig->get()->animationIn,
            'dialogAnimationOut' => $this->dialogSettingsConfig->get()->animationOut,
            'dialogButtonDetailsOrder' => $this->handleButtonOrder($this->dialogSettingsConfig->get()->buttonDetailsOrder),
            'dialogButtonEntranceOrder' => $this->handleButtonOrder($this->dialogSettingsConfig->get()->buttonEntranceOrder),
            'dialogButtonSwitchRound' => $this->dialogSettingsConfig->get()->buttonSwitchRound,
            'dialogEnableBackdrop' => $this->dialogSettingsConfig->get()->enableBackdrop,
            'dialogGeoIpActive' => $this->dialogSettingsConfig->get()->geoIpActive,
            'dialogGeoIpCachingMode' => $this->dialogSettingsConfig->get()->geoIpCachingMode,
            'dialogHideDialogOnPages' => $this->dialogSettingsConfig->get()->hideDialogOnPages,
            'dialogImprintLink' => $dialogImprintLink,
            'dialogLayout' => $this->dialogSettingsConfig->get()->layout,
            'dialogLegalInformationDescriptionConfirmAgeStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionConfirmAgeStatus,
            'dialogLegalInformationDescriptionIndividualSettingsStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionIndividualSettingsStatus,
            'dialogLegalInformationDescriptionMoreInformationStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionMoreInformationStatus,
            'dialogLegalInformationDescriptionNonEuDataTransferStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionNonEuDataTransferStatus,
            'dialogLegalInformationDescriptionNoObligationStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionNoObligationStatus,
            'dialogLegalInformationDescriptionPersonalDataStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionPersonalDataStatus,
            'dialogLegalInformationDescriptionRevokeStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionRevokeStatus,
            'dialogLegalInformationDescriptionTechnologyStatus' => $this->dialogSettingsConfig->get()->legalInformationDescriptionTechnologyStatus,
            'dialogLogoSrcSet' => $dialogLogoSrcSet,
            'dialogPosition' => $this->dialogSettingsConfig->get()->position,
            'dialogPrivacyLink' => $dialogPrivacyLink,
            'dialogServiceGroupJustification' => $this->dialogSettingsConfig->get()->serviceGroupJustification,
            'dialogShowAcceptAllButton' => $this->dialogSettingsConfig->get()->showAcceptAllButton,
            'dialogShowAcceptOnlyEssentialButton' => $this->dialogSettingsConfig->get()->showAcceptOnlyEssentialButton,
            'dialogShowDialog' => $this->dialogSettingsConfig->get()->showDialog,
            'showHeadlineSeparator' => $this->dialogSettingsConfig->get()->showHeadlineSeparator,
            'dialogShowLogo' => $this->dialogSettingsConfig->get()->showLogo,
            'dialogSupportBorlabsCookieLogo' => $supportBorlabsCookieLogo,
            'dialogSupportBorlabsCookieStatus' => $this->dialogSettingsConfig->get()->showBorlabsCookieBranding,
            'dialogSupportBorlabsCookieText' => $this->defaultLocalizationStrings->get()['dialog']['supportBorlabsCookieText'],
            'dialogSupportBorlabsCookieUrl' => $this->defaultLocalizationStrings->get()['dialog']['supportBorlabsCookieUrl'],
            'dialogUid' => $this->defaultLocalizationStrings->get()['dialog']['uid'],

            'globalStorageUrl' => $this->globalStorageFolder->getUrl(),
            'iabTcfStatus' => $this->iabTcfConfig->get()->iabTcfStatus,
            'language' => $languageCode,
            'pluginUrl' => BORLABS_COOKIE_PLUGIN_URL,
            'pluginVersion' => BORLABS_COOKIE_VERSION,
            'production' => defined('BORLABS_COOKIE_DEV_MODE') && constant('BORLABS_COOKIE_DEV_MODE') === true ? false : true,
            'reloadAfterOptIn' => $this->generalConfig->get()->reloadAfterOptIn,
            'reloadAfterOptOut' => $this->generalConfig->get()->reloadAfterOptOut,
            'respectDoNotTrack' => $this->generalConfig->get()->respectDoNotTrack,

            'widgetIcon' => BORLABS_COOKIE_PLUGIN_URL . '/assets/images/' . $this->widgetConfig->get()->icon,
            'widgetPosition' => $this->widgetConfig->get()->position,
            'widgetShow' => $this->widgetConfig->get()->show,

            'wpRestURL' => $this->wpFunction->escUrlRaw($this->wpFunction->restUrl()),
        ];

        $globalStrings = (array) $this->dialogLocalization->get();
        $globalStrings['entranceHeadline'] = esc_attr($globalStrings['entranceHeadline']);
        $globalStrings['entranceDescription'] = do_shortcode($globalStrings['entranceDescription']);
        $globalStrings['detailsHeadline'] = esc_attr($globalStrings['detailsHeadline']);
        $globalStrings['detailsDescription'] = do_shortcode($globalStrings['detailsDescription']);

        $borlabsCookieConfig = [
            'contentBlockers' => $this->getContentBlockers($languageCode),
            'globalStrings' => $globalStrings,
            'providers' => $this->getProviders($languageCode),
            'serviceGroups' => $this->getServiceGroups($languageCode),
            'services' => $this->getServices($languageCode),
            'settings' => $settings,
            'tcfVendors' => array_map(fn ($data) => (int) $data->key, $this->iabTcfConfig->get()->vendors->list ?? []),
        ];

        $this->fileManager->cacheFile(
            $this->getConfigFileName($languageCode),
            '/* Temp: ' . date('Y-m-d H:i:s') . ' */' .
            'var borlabsCookieConfig = (function () { return JSON.parse("' . addslashes(
                json_encode($borlabsCookieConfig),
            ) . '"); })();',
        );
    }

    public function getConfigFileName(string $languageCode): string
    {
        return 'borlabs-cookie-config-' . $languageCode . '.json.js';
    }

    private function getContentBlockerData(ContentBlockerModel $contentBlocker): array
    {
        return [
            'description' => esc_html($contentBlocker->description),
            'javaScriptGlobal' => esc_html($contentBlocker->javaScriptGlobal),
            'javaScriptInitialization' => esc_html($contentBlocker->javaScriptInitialization),
            'settings' => array_column($contentBlocker->settingsFields->list, 'value', 'key'),
            'hosts' => $this->getContentBlockerLocations($contentBlocker),
            'id' => esc_html($contentBlocker->key),
            'name' => esc_html($contentBlocker->name),
            'providerId' => esc_html($contentBlocker->provider->key),
            'serviceId' => isset($contentBlocker->service->key) ? esc_html($contentBlocker->service->key) : null,
        ];
    }

    private function getContentBlockerLocations(ContentBlockerModel $contentBlocker): array
    {
        $contentBlockerLocations = [];

        foreach ($contentBlocker->contentBlockerLocations as $contentBlockerLocation) {
            $contentBlockerLocations[] = [
                'hostname' => esc_html($contentBlockerLocation->hostname),
            ];
        }

        return $contentBlockerLocations;
    }

    private function getContentBlockers(string $languageCode): array
    {
        $contentBlockers = [];
        $contentBlockerModels = $this->contentBlockerRepository->find(
            [
                'language' => $languageCode,
                'status' => 1,
            ],
            ['name' => 'ASC',],
            [],
            ['contentBlockerLocations', 'provider', 'service'],
        );

        if (empty($contentBlockerModels)) {
            return $contentBlockers;
        }

        foreach ($contentBlockerModels as $contentBlocker) {
            $contentBlockers[$contentBlocker->key] = $this->getContentBlockerData($contentBlocker);
        }

        return $contentBlockers;
    }

    private function getProviderData(ProviderModel $provider): array
    {
        return [
            'address' => esc_html($provider->address),
            'contentBlockerIds' => array_map(function (ContentBlockerModel $contentBlocker) {
                return $contentBlocker->status ? $contentBlocker->key : null;
            }, $provider->contentBlockers),
            'cookieUrl' => esc_url($provider->cookieUrl),
            'description' => esc_html($provider->description),
            'iabVendorId' => $provider->iabVendorId,
            'id' => esc_html($provider->key),
            'name' => esc_html($provider->name),
            'optOutUrl' => esc_html($provider->optOutUrl),
            'partners' => esc_html(implode(', ', $provider->partners ?? [])),
            'privacyUrl' => esc_url($provider->privacyUrl),
            'serviceIds' => array_map(function (ServiceModel $service) {
                return $service->status ? $service->key : null;
            }, $provider->services),
        ];
    }

    private function getProviders(string $languageCode): array
    {
        $providers = [];
        $providerModels = $this->providerRepository->find(
            ['language' => $languageCode,],
            ['name' => 'ASC',],
            [],
            [
                'contentBlockers' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                },
                'services' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->addWith('serviceGroup', function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                        $queryBuilder->andWhere(new BinaryOperatorExpression(
                            new ModelFieldNameExpression('status'),
                            '=',
                            new LiteralExpression(1),
                        ));
                    });
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                },
            ],
        );

        foreach ($providerModels as $provider) {
            $servicesWithActiveServiceGroup = array_filter(
                $provider->services,
                function (ServiceModel $service) {
                    return $service->serviceGroup ?? null;
                },
            );

            if (
                count($provider->contentBlockers) === 0
                && (
                    count($provider->services) === 0
                    || count($servicesWithActiveServiceGroup) === 0
                )
                && $provider->iabVendorId === null
            ) {
                continue;
            }

            $providers[$provider->key] = $this->getProviderData($provider);
        }

        return $providers;
    }

    private function getServiceCookies(ServiceModel $service): array
    {
        $serviceCookies = [];

        foreach ($service->serviceCookies as $serviceCookie) {
            $serviceCookies[] = [
                'description' => esc_html($serviceCookie->description),
                'hostname' => esc_html($serviceCookie->hostname),
                'lifetime' => esc_html($serviceCookie->lifetime),
                'name' => esc_html($serviceCookie->name),
                'purpose' => esc_html($serviceCookie->purpose),
                'type' => esc_html($serviceCookie->type),
            ];
        }

        return $serviceCookies;
    }

    private function getServiceData(ServiceModel $service): array
    {
        $search = array_map(static fn ($value) => '{{ ' . $value . ' }}', array_column($service->settingsFields->list, 'key'));
        $replace = array_column($service->settingsFields->list, 'value');

        return [
            'cookies' => $this->getServiceCookies($service),
            'description' => esc_html($service->description),
            'hosts' => $this->getServiceLocations($service),
            'id' => esc_html($service->key),
            'name' => esc_html($service->name),
            'optInCode' => $service->optInCode !== '' ? base64_encode(do_shortcode(str_replace($search, $replace, $service->optInCode))) : '',
            'options' => $this->getServiceOptions($service),
            'optOutCode' => $service->optInCode !== '' ? base64_encode(do_shortcode(str_replace($search, $replace, $service->optInCode))) : '',
            'providerId' => esc_html($service->provider->key),
            'serviceGroupId' => esc_html($service->serviceGroup->key),
            'settings' => array_column($service->settingsFields->list, 'value', 'key'),
        ];
    }

    private function getServiceGroups(string $languageCode): array
    {
        $serviceGroups = [];
        $serviceGroupModels = $this->serviceGroupRepository->find(
            [
                'language' => $languageCode,
                'status' => 1,
            ],
            ['position' => 'ASC',],
            [],
            ['services'],
        );

        if (empty($serviceGroupModels)) {
            return $serviceGroups;
        }

        foreach ($serviceGroupModels as $serviceGroupData) {
            if (count($serviceGroupData->services) === 0) {
                continue;
            }

            // Only add service group when there are services with status true
            $serviceIds = array_map(
                fn ($service) => $service->key,
                array_filter(
                    $serviceGroupData->services,
                    fn ($service) => $service->status === true,
                ),
            );

            if (count($serviceIds) === 0) {
                continue;
            }

            $serviceGroups[$serviceGroupData->key] = [
                'description' => nl2br($serviceGroupData->description),
                'id' => $serviceGroupData->key,
                'name' => $serviceGroupData->name,
                'preSelected' => !empty($serviceGroupData->preSelected),
                'serviceIds' => $serviceIds,
            ];
        }

        return $serviceGroups;
    }

    private function getServiceLocations(ServiceModel $service): array
    {
        $serviceLocations = [];

        foreach ($service->serviceLocations as $serviceLocation) {
            $serviceLocations[] = [
                'hostname' => esc_html($serviceLocation->hostname),
            ];
        }

        return $serviceLocations;
    }

    private function getServiceOptions(ServiceModel $service): array
    {
        $serviceOptions = [];

        foreach ($service->serviceOptions as $serviceOption) {
            $serviceOptions[] = [
                'name' => esc_html($serviceOption->description),
                'type' => esc_html($serviceOption->type),
            ];
        }

        return $serviceOptions;
    }

    private function getServices(string $languageCode): array
    {
        $services = [];
        $serviceModels = $this->serviceRepository->find(
            [
                'language' => $languageCode,
                'status' => 1,
            ],
            ['position' => 'ASC'],
            [],
            [
                'provider',
                'serviceCookies',
                'serviceGroup' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                },
                'serviceLocations',
                'serviceOptions',
            ],
        );

        if (empty($serviceModels)) {
            return $services;
        }

        foreach ($serviceModels as $serviceData) {
            if (!isset($serviceData->serviceGroup)) {
                continue;
            }

            $services[$serviceData->key] = $this->getServiceData($serviceData);
        }

        return $services;
    }

    private function handleButtonOrder(array $buttonOrder): array
    {
        $newButtonOrder = [];
        $index = 0;

        foreach ($buttonOrder as $button) {
            if (($button === 'all' && $this->dialogSettingsConfig->get()->showAcceptAllButton === true)
                || ($button === 'essential' && $this->dialogSettingsConfig->get()->showAcceptOnlyEssentialButton === true)
                || $button !== 'essential') {
                $newButtonOrder[$index] = $button;
                ++$index;
            }
        }

        return $newButtonOrder;
    }
}
