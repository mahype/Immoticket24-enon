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

namespace Borlabs\Cookie\System\ContentBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\ContentBlocker\ContentBlockerDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\Service\ServiceService;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\ContentBlocker\ContentBlockerLanguageStringValidator;
use Borlabs\Cookie\Validator\ContentBlocker\ContentBlockerValidator;

class ContentBlockerService
{
    use SettingsFieldListTrait;

    private ContentBlockerDefaultEntries $contentBlockerDefaultEntries;

    private ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFields;

    private ContentBlockerLanguageStringValidator $contentBlockerLanguageStringValidator;

    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerLocationService $contentBlockerLocationService;

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerValidator $contentBlockerValidator;

    private Language $language;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ServiceRepository $serviceRepository;

    private ServiceService $serviceService;

    private StyleBuilder $styleBuilder;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    /**
     * ContentBlockerService constructor.
     */
    public function __construct(
        ContentBlockerDefaultEntries $contentBlockerDefaultEntries,
        ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFields,
        ContentBlockerLanguageStringValidator $contentBlockerLanguageStringValidator,
        ContentBlockerLocationRepository $contentBlockerLocationRepository,
        ContentBlockerLocationService $contentBlockerLocationService,
        ContentBlockerRepository $contentBlockerRepository,
        ContentBlockerValidator $contentBlockerValidator,
        Language $language,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ServiceRepository $serviceRepository,
        ServiceService $serviceService,
        StyleBuilder $styleBuilder,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerDefaultEntries = $contentBlockerDefaultEntries;
        $this->contentBlockerDefaultSettingsFields = $contentBlockerDefaultSettingsFields;
        $this->contentBlockerLanguageStringValidator = $contentBlockerLanguageStringValidator;
        $this->contentBlockerLocationRepository = $contentBlockerLocationRepository;
        $this->contentBlockerLocationService = $contentBlockerLocationService;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->contentBlockerValidator = $contentBlockerValidator;
        $this->language = $language;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->serviceRepository = $serviceRepository;
        $this->serviceService = $serviceService;
        $this->styleBuilder = $styleBuilder;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function getOrCreateContentBlockerPerLanguage(ContentBlockerModel $contentBlocker, array $languages): KeyValueDtoList
    {
        // Get model with relations
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlocker->id, ['contentBlockerLocations',]);

        $contentBlockers = $this->contentBlockerRepository->getAllByKey($contentBlocker->key);
        $contentBlockerPerLanguageList = new KeyValueDtoList();
        $missingLanguages = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $contentBlockerModel = Searcher::findObject($contentBlockers, 'language', $languageCode);

            if (($contentBlockerModel->id ?? null) === $contentBlocker->id) {
                continue;
            }

            if ($contentBlockerModel !== null) {
                $contentBlockerPerLanguageList->add(new KeyValueDto($languageCode, (string) $contentBlockerModel->id));
            } else {
                $missingLanguages->add(new KeyValueDto($languageCode, $languageCode));
            }
        }

        // Create missing languages of ContentBlocker
        if (count($missingLanguages->list)) {
            /** @var ProviderModel $provider */
            $provider = $this->providerRepository->findById($contentBlocker->providerId);
            $providerPerLanguageList = $this->providerService->getOrCreateProviderPerLanguage($provider, $languages);

            $servicePerLanguageList = new KeyValueDtoList();

            if (isset($contentBlocker->serviceId)) {
                /** @var ServiceModel $service */
                $service = $this->serviceRepository->findById($contentBlocker->serviceId);
                $servicePerLanguageList = $this->serviceService->getOrCreateServicePerLanguage($service, $languages);
            }

            $postLanguageStrings = [];

            foreach ($contentBlocker->languageStrings->list as $index => $languageString) {
                $postLanguageStrings[$index]['key'] = $languageString->key;
                $postLanguageStrings[$index]['value'] = $languageString->value;
            }

            $postSettingsFields = [];

            foreach ($contentBlocker->settingsFields->list as $settingsField) {
                $postSettingsFields[$settingsField->formFieldCollectionName][$settingsField->key] = $settingsField->value;
            }

            $newContentBlockerPerLanguage = $this->handleAdditionalLanguages(
                array_column($missingLanguages->list, 'key'),
                [
                    'borlabsServicePackageKey' => $contentBlocker->borlabsServicePackageKey ?? null,
                    'javaScriptGlobal' => $contentBlocker->javaScriptGlobal,
                    'javaScriptInitialization' => $contentBlocker->javaScriptInitialization,
                    'key' => $contentBlocker->key,
                    'languageStrings' => $postLanguageStrings,
                    'name' => $contentBlocker->name,
                    'previewCss' => $contentBlocker->previewCss,
                    'previewHtml' => $contentBlocker->previewHtml,
                    'previewImage' => $contentBlocker->previewImage,
                    'settingsFields' => $postSettingsFields,
                    'status' => $contentBlocker->status,
                ],
                $providerPerLanguageList,
                $servicePerLanguageList,
            );

            foreach ($newContentBlockerPerLanguage->list as $languageContentBlockerId) {
                $contentBlockerPerLanguageList->add(new KeyValueDto($languageContentBlockerId->key, (string) $languageContentBlockerId->value));
            }

            if (isset($contentBlocker->contentBlockerLocations)) {
                foreach ($contentBlockerPerLanguageList->list as $contentBlockerIdPerLanguage) {
                    /** @var ContentBlockerModel $contentBlockerPerLanguage */
                    $contentBlockerPerLanguage = $this->contentBlockerRepository->findById((int) $contentBlockerIdPerLanguage->value);

                    if (!isset($contentBlockerPerLanguage)) {
                        continue;
                    }

                    $this->contentBlockerLocationService->save(
                        $contentBlockerPerLanguage,
                        array_map(static fn ($hostData) => (array) $hostData, $contentBlocker->contentBlockerLocations),
                    );
                }
            }
        }

        return $contentBlockerPerLanguageList;
    }

    public function handleAdditionalLanguages(
        array $languages,
        array $postData,
        KeyValueDtoList $providerPerLanguageList,
        KeyValueDtoList $servicePerLanguageList
    ): KeyValueDtoList {
        $sourceTexts = new KeyValueDtoList();
        $sourceTexts->add(new KeyValueDto('previewHtml', $postData['previewHtml']));

        if (isset($postData['languageStrings']) && is_array($postData['languageStrings'])) {
            foreach ($postData['languageStrings'] as $languageString) {
                $sourceTexts->add(new KeyValueDto('languageString_' . $languageString['key'], $languageString['value']));
            }
        }

        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            $languages,
            $sourceTexts,
        );

        $contentBlockerPerLanguageList = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;
            $previewHtml = $postData['previewHtml'];
            $languageStrings = $postData['languageStrings'];

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                $previewHtml = array_column($translation->translations->list, 'value', 'key')['previewHtml'] ?? $previewHtml;

                foreach ($postData['languageStrings'] as $index => $languageString) {
                    $languageStrings[$index]['value'] = array_column($translation->translations->list, 'value', 'key')['languageString_' . $languageString['key']] ?? $languageString['value'];
                }
            }

            $providerId = Searcher::findObject($providerPerLanguageList->list, 'key', $languageCode)->value ?? null;

            if (!isset($providerId)) {
                continue;
            }

            $serviceId = Searcher::findObject($servicePerLanguageList->list, 'key', $languageCode)->value ?? 0;

            $postData['id'] = '-1';
            $postData['languageStrings'] = $languageStrings;
            $postData['previewHtml'] = $previewHtml;
            $postData['providerId'] = (string) $providerId;
            $postData['serviceId'] = (string) $serviceId;

            $contentBlockerId = $this->save(
                -1,
                $languageCode,
                $postData,
            );

            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageCode,
            );

            if ($contentBlockerId !== null) {
                $contentBlockerPerLanguageList->add(new KeyValueDto($languageCode, $contentBlockerId));
            }
        }

        return $contentBlockerPerLanguageList;
    }

    public function reset(): bool
    {
        // Todo: Test if language switch works
        $this->language->loadBlogLanguage();

        foreach ($this->contentBlockerDefaultEntries->getDefaultEntries() as $defaultContentBlockerModel) {
            $contentBlockerModel = $this->contentBlockerRepository->getByKey(
                $defaultContentBlockerModel->key,
                null,
                true,
            );

            if ($contentBlockerModel) {
                $defaultContentBlockerModel->id = $contentBlockerModel->id;

                $this->contentBlockerRepository->update($defaultContentBlockerModel);
            } else {
                $contentBlockerModel = $this->contentBlockerRepository->insert($defaultContentBlockerModel);
            }

            // Delete content blocker locations
            if (isset($contentBlockerModel->contentBlockerLocations)) {
                foreach ($contentBlockerModel->contentBlockerLocations as $contentBlockerLocation) {
                    $this->contentBlockerLocationRepository->delete($contentBlockerLocation);
                }
            }
        }

        $this->language->unloadBlogLanguage();

        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );

        return true;
    }

    public function save(int $id, string $languageCode, array $postData): ?int
    {
        if (
            !$this->contentBlockerValidator->isValid($postData, $languageCode)
            || !$this->contentBlockerLanguageStringValidator->isValid($postData)
        ) {
            return null;
        }

        $postData = Sanitizer::requestData($postData);
        $postData = $this->wpFunction->applyFilter('borlabsCookie/contentBlocker/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->contentBlockerRepository->findById($id);
        }

        $languageStrings = new KeyValueDtoList();

        if (isset($postData['languageStrings']) && is_array($postData['languageStrings'])) {
            foreach ($postData['languageStrings'] as $languageString) {
                $languageStrings->add(new KeyValueDto($languageString['key'], $languageString['value']));
            }
        }

        $settingsFields = $existingModel->settingsFields ?? new SettingsFieldDtoList();
        $defaultSettingsFields = $this->contentBlockerDefaultSettingsFields->get($languageCode);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        $newModel = new ContentBlockerModel();
        $newModel->id = $id;
        $newModel->borlabsServicePackageKey = $existingModel->borlabsServicePackageKey ?? $postData['borlabsServicePackageKey'] ?? null;
        $newModel->description = $existingModel->description ?? '';
        $newModel->javaScriptGlobal = $postData['javaScriptGlobal'] ?? '';
        $newModel->javaScriptInitialization = $postData['javaScriptInitialization'] ?? '';
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->languageStrings = $languageStrings;
        $newModel->name = $postData['name'];
        $newModel->previewCss = $postData['previewCss'] ?? '';
        $newModel->previewHtml = $postData['previewHtml'] ?? '';
        $newModel->previewImage = $postData['previewImage'] ?? '';
        $newModel->providerId = (int) $postData['providerId'];
        $newModel->serviceId = !empty($postData['serviceId']) && $postData['serviceId'] !== '0' ? (int) $postData['serviceId'] : null;
        $newModel->settingsFields = $settingsFields;

        foreach ($postData['settingsFields'] as $settingsFieldsPostData) {
            $newModel->settingsFields = $this->updateSettingsValuesFromFormFields($newModel->settingsFields, $settingsFieldsPostData);
        }

        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = $existingModel->undeletable ?? false;

        if ($newModel->id !== -1) {
            $this->contentBlockerRepository->update($newModel);
        } else {
            $newModel = $this->contentBlockerRepository->insert($newModel);
        }

        return $newModel->id;
    }
}
