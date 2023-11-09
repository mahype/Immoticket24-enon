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

namespace Borlabs\Cookie\System\Service;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\Service\ServiceDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\ServiceGroup\ServiceGroupService;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Service\ServiceValidator;

class ServiceService
{
    use SettingsFieldListTrait;

    private Language $language;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceCookieService $serviceCookieService;

    private ServiceDefaultEntries $serviceDefaultEntries;

    private ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupService $serviceGroupService;

    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceLocationService $serviceLocationService;

    private ServiceOptionRepository $serviceOptionRepository;

    private ServiceOptionService $serviceOptionService;

    private ServiceRepository $serviceRepository;

    private ServiceValidator $serviceValidator;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceCookieService $serviceCookieService,
        ServiceDefaultEntries $serviceDefaultEntries,
        ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupService $serviceGroupService,
        ServiceLocationRepository $serviceLocationRepository,
        ServiceLocationService $serviceLocationService,
        ServiceOptionRepository $serviceOptionRepository,
        ServiceOptionService $serviceOptionService,
        ServiceRepository $serviceRepository,
        ServiceValidator $serviceValidator,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceCookieService = $serviceCookieService;
        $this->serviceDefaultEntries = $serviceDefaultEntries;
        $this->serviceDefaultSettingsFieldManager = $serviceDefaultSettingsFieldManager;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupService = $serviceGroupService;
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceLocationService = $serviceLocationService;
        $this->serviceOptionRepository = $serviceOptionRepository;
        $this->serviceOptionService = $serviceOptionService;
        $this->serviceRepository = $serviceRepository;
        $this->serviceValidator = $serviceValidator;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function getOrCreateServicePerLanguage(ServiceModel $service, array $languages): KeyValueDtoList
    {
        // Get model with relations
        $service = $this->serviceRepository->findById($service->id, ['serviceCookies', 'serviceLocations', 'serviceOptions']);

        $services = $this->serviceRepository->getAllByKey($service->key);
        $servicePerLanguageList = new KeyValueDtoList();
        $missingLanguages = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $serviceModel = Searcher::findObject($services, 'language', $languageCode);

            if (($serviceModel->id ?? null) === $service->id) {
                continue;
            }

            if ($serviceModel !== null) {
                $servicePerLanguageList->add(new KeyValueDto($languageCode, (string) $serviceModel->id));
            } else {
                $missingLanguages->add(new KeyValueDto($languageCode, $languageCode));
            }
        }

        // Create missing languages of Service
        if (count($missingLanguages->list)) {
            $provider = $this->providerRepository->findById($service->providerId);
            $providerPerLanguageList = $this->providerService->getOrCreateProviderPerLanguage($provider, $languages);
            $serviceGroup = $this->serviceGroupRepository->findById($service->serviceGroupId);
            $serviceGroupPerLanguageList = $this->serviceGroupService->getOrCreateServiceGroupsPerLanguage($serviceGroup, $languages);

            $postSettingsFields = [];

            foreach ($service->settingsFields->list as $settingsField) {
                $postSettingsFields[$settingsField->formFieldCollectionName][$settingsField->key] = $settingsField->value;
            }

            $newServicesPerLanguage = $this->handleAdditionalLanguages(
                array_column($missingLanguages->list, 'key'),
                [
                    'borlabsServicePackageKey' => $service->borlabsServicePackageKey ?? null,
                    'description' => $service->description,
                    'fallbackCode' => $service->fallbackCode,
                    'key' => $service->key,
                    'name' => $service->name,
                    'optInCode' => $service->optInCode,
                    'optOutCode' => $service->optOutCode,
                    'position' => $service->position,
                    'settingsFields' => $postSettingsFields,
                    'status' => $service->status,
                ],
                $providerPerLanguageList,
                $serviceGroupPerLanguageList,
            );

            foreach ($newServicesPerLanguage->list as $languageServiceId) {
                $servicePerLanguageList->add(new KeyValueDto($languageServiceId->key, (string) $languageServiceId->value));
            }

            if (isset($service->serviceCookies)) {
                $this->serviceCookieService->handleAdditionalLanguages(
                    $languages,
                    array_map(static fn ($cookieData) => (array) $cookieData, $service->serviceCookies),
                    $servicePerLanguageList,
                );
            }

            if (isset($service->serviceLocations)) {
                foreach ($servicePerLanguageList->list as $serviceIdPerLanguage) {
                    $servicePerLanguage = $this->serviceRepository->findById((int) $serviceIdPerLanguage->value);

                    if (!isset($servicePerLanguage)) {
                        continue;
                    }

                    $this->serviceLocationService->save(
                        $servicePerLanguage,
                        array_map(static fn ($hostData) => (array) $hostData, $service->serviceLocations),
                    );
                }
            }

            if (isset($service->serviceOptions)) {
                $this->serviceOptionService->handleAdditionalLanguages(
                    $languages,
                    array_map(static fn ($optionData) => (array) $optionData, $service->serviceOptions),
                    $servicePerLanguageList,
                );
            }
        }

        return $servicePerLanguageList;
    }

    public function handleAdditionalLanguages(
        array $languages,
        array $postData,
        KeyValueDtoList $providerPerLanguageList,
        KeyValueDtoList $serviceGroupPerLanguageList
    ): KeyValueDtoList {
        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            $languages,
            new KeyValueDtoList([
                new KeyValueDto('description', $postData['description']),
            ]),
        );

        $servicePerLanguageList = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;
            $description = $postData['description'];

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                $description = array_column($translation->translations->list, 'value', 'key')['description'] ?? $description;
            }

            $providerId = Searcher::findObject($providerPerLanguageList->list, 'key', $languageCode)->value ?? null;
            $serviceGroupId = Searcher::findObject($serviceGroupPerLanguageList->list, 'key', $languageCode)->value ?? null;

            if (!isset($providerId, $serviceGroupId)) {
                continue;
            }

            $postData['description'] = $description;
            $postData['id'] = '-1';
            $postData['providerId'] = (string) $providerId;
            $postData['serviceGroupId'] = (string) $serviceGroupId;
            $serviceId = $this->save(
                -1,
                $languageCode,
                $postData,
            );

            if ($serviceId !== null) {
                $servicePerLanguageList->add(new KeyValueDto($languageCode, $serviceId));
            }
        }

        return $servicePerLanguageList;
    }

    public function reset(): bool
    {
        // Todo: Test if language switch works
        $this->language->loadBlogLanguage();

        foreach ($this->serviceDefaultEntries->getDefaultEntries() as $defaultServiceModel) {
            $serviceModel = $this->serviceRepository->getByKey(
                $defaultServiceModel->key,
                null,
                true,
            );

            if ($serviceModel) {
                $defaultServiceModel->id = $serviceModel->id;
                $this->serviceRepository->update($defaultServiceModel);
            } else {
                $serviceModel = $this->serviceRepository->insert($defaultServiceModel);
            }

            // Delete service cookies
            if (isset($serviceModel->serviceCookies)) {
                foreach ($serviceModel->serviceCookies as $serviceCookie) {
                    $this->serviceCookieRepository->delete($serviceCookie);
                }
            }

            // Delete service locations
            if (isset($serviceModel->serviceLocations)) {
                foreach ($serviceModel->serviceLocations as $serviceLocation) {
                    $this->serviceLocationRepository->delete($serviceLocation);
                }
            }

            // Delete service options
            if (isset($serviceModel->serviceOptions)) {
                foreach ($serviceModel->serviceOptions as $serviceOption) {
                    $this->serviceOptionRepository->delete($serviceOption);
                }
            }

            // Add service cookies
            if (isset($defaultServiceModel->serviceCookies)) {
                foreach ($defaultServiceModel->serviceCookies as $serviceCookie) {
                    $serviceCookie->serviceId = $serviceModel->id;
                    $this->serviceCookieRepository->insert($serviceCookie);
                }
            }
        }

        $this->language->unloadBlogLanguage();

        return true;
    }

    public function save(int $id, string $languageCode, array $postData): ?int
    {
        if (!$this->serviceValidator->isValid($postData, $languageCode)) {
            return null;
        }

        $postData = Sanitizer::requestData($postData);
        $postData = $this->wpFunction->applyFilter('borlabsCookie/service/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->serviceRepository->findById($id);
        }

        $settingsFields = $existingModel->settingsFields ?? new SettingsFieldDtoList();
        $defaultSettingsFields = $this->serviceDefaultSettingsFieldManager->get($languageCode);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        $newModel = new ServiceModel();
        $newModel->id = $id;
        $newModel->borlabsServicePackageKey = $existingModel->borlabsServicePackageKey ?? $postData['borlabsServicePackageKey'] ?? null;
        $newModel->description = $postData['description'];
        $newModel->fallbackCode = $postData['fallbackCode'];
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->name = $postData['name'];
        $newModel->optInCode = $postData['optInCode'];
        $newModel->optOutCode = $postData['optOutCode'];
        $newModel->position = (int) $postData['position'];
        $newModel->providerId = (int) $postData['providerId'];
        $newModel->settingsFields = $settingsFields;

        foreach ($postData['settingsFields'] as $settingsFieldsPostData) {
            $newModel->settingsFields = $this->updateSettingsValuesFromFormFields($newModel->settingsFields, $settingsFieldsPostData);
        }

        $newModel->serviceGroupId = (int) $postData['serviceGroupId'];
        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = $existingModel->undeletable ?? false;

        if ($newModel->id !== -1) {
            $this->serviceRepository->update($newModel);
        } else {
            $newModel = $this->serviceRepository->insert($newModel);
        }

        return $newModel->id;
    }
}
