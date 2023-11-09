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

namespace Borlabs\Cookie\System\Provider;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\Provider\ProviderDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Provider\ProviderValidator;

final class ProviderService
{
    private Language $language;

    private ProviderDefaultEntries $providerDefaultEntries;

    private ProviderRepository $providerRepository;

    private ProviderValidator $providerValidator;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ProviderDefaultEntries $providerDefaultEntries,
        ProviderRepository $providerRepository,
        ProviderValidator $providerValidator,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->providerDefaultEntries = $providerDefaultEntries;
        $this->providerRepository = $providerRepository;
        $this->providerValidator = $providerValidator;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function getOrCreateProviderPerLanguage(ProviderModel $provider, array $languages): KeyValueDtoList
    {
        $providers = $this->providerRepository->getAllByKey($provider->key);
        $providersPerLanguage = new KeyValueDtoList();
        $missingLanguages = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $providerModel = Searcher::findObject($providers, 'language', $languageCode);

            if (($providerModel->id ?? null) === $provider->id) {
                continue;
            }

            if ($providerModel !== null) {
                $providersPerLanguage->add(new KeyValueDto($languageCode, (string) $providerModel->id));
            } else {
                $missingLanguages->add(new KeyValueDto($languageCode, $languageCode));
            }
        }

        // Create missing languages of the Provider
        if (count($missingLanguages->list)) {
            $newProvidersPerLanguage = $this->handleAdditionalLanguages(
                array_column($missingLanguages->list, 'key'),
                [
                    'address' => $provider->address,
                    'borlabsServiceProviderKey' => $provider->borlabsServiceProviderKey ?? null,
                    'cookieUrl' => $provider->cookieUrl,
                    'description' => $provider->description,
                    'iabVendorId' => $provider->iabVendorId,
                    'key' => $provider->key,
                    'name' => $provider->name,
                    'optOutUrl' => $provider->optOutUrl,
                    'partners' => $provider->partners !== null ? implode("\n", $provider->partners) : '',
                    'privacyUrl' => $provider->privacyUrl,
                ],
            );

            foreach ($newProvidersPerLanguage->list as $languageServiceGroupId) {
                $providersPerLanguage->add(new KeyValueDto($languageServiceGroupId->key, (string) $languageServiceGroupId->value));
            }
        }

        return $providersPerLanguage;
    }

    public function handleAdditionalLanguages(array $languages, array $postData): KeyValueDtoList
    {
        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            $languages,
            new KeyValueDtoList([
                new KeyValueDto('description', $postData['description']),
            ]),
        );

        $providerPerLanguageList = new KeyValueDtoList();

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

            $postData['description'] = $description;
            $postData['id'] = '-1';
            $providerId = $this->save(
                -1,
                $languageCode,
                $postData,
            );

            if ($providerId !== null) {
                $providerPerLanguageList->add(new KeyValueDto($languageCode, $providerId));
            }
        }

        return $providerPerLanguageList;
    }

    public function reset(): bool
    {
        foreach ($this->providerDefaultEntries->getDefaultEntries() as $defaultProviderModel) {
            if (isset($defaultProviderModel->borlabsServiceProviderKey)) {
                $providerModel = $this->providerRepository->getByBorlabsServiceProviderKey($defaultProviderModel->borlabsServiceProviderKey);
            } else {
                $providerModel = $this->providerRepository->getByKey($defaultProviderModel->key);
            }

            if ($providerModel) {
                $defaultProviderModel->id = $providerModel->id;

                $this->providerRepository->update($defaultProviderModel);
            } else {
                $this->providerRepository->insert($defaultProviderModel);
            }
        }

        return true;
    }

    public function save(int $id, string $languageCode, array $postData): ?int
    {
        if (!$this->providerValidator->isValid($postData, $languageCode)) {
            return null;
        }

        $postData = Sanitizer::requestData($postData);
        $postData = $this->wpFunction->applyFilter('borlabsCookie/provider/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->providerRepository->findById($id);
        }

        $newModel = new ProviderModel();
        $newModel->id = $id;
        $newModel->address = $postData['address'];
        $newModel->borlabsServiceProviderKey = $existingModel->borlabsServiceProviderKey ?? $postData['borlabsServiceProviderKey'] ?? null;
        $newModel->cookieUrl = $postData['cookieUrl'];
        $newModel->description = $postData['description'];
        $newModel->iabVendorId = !empty($postData['iabVendorId']) && $postData['iabVendorId'] !== '0' ? (int) $postData['iabVendorId'] : null;
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->name = $postData['name'];
        $newModel->optOutUrl = $postData['optOutUrl'];
        $newModel->partners = $postData['partners'] ? explode("\n", $postData['partners']) : [];
        $newModel->privacyUrl = $postData['privacyUrl'];
        $newModel->undeletable = $existingModel->undeletable ?? false;

        if ($newModel->id !== -1) {
            $this->providerRepository->update($newModel);
        } else {
            $newModel = $this->providerRepository->insert($newModel);
        }

        return $newModel->id;
    }
}
