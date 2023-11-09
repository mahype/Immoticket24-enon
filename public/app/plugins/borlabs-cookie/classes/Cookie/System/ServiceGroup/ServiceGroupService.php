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

namespace Borlabs\Cookie\System\ServiceGroup;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\ServiceGroup\ServiceGroupDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\ServiceGroup\ServiceGroupValidator;

class ServiceGroupService
{
    private Language $language;

    private ServiceGroupDefaultEntries $serviceGroupDefaultEntries;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupValidator $serviceGroupValidator;

    private ServiceRepository $serviceRepository;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ServiceGroupDefaultEntries $serviceGroupDefaultEntries,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupValidator $serviceGroupValidator,
        ServiceRepository $serviceRepository,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->serviceGroupDefaultEntries = $serviceGroupDefaultEntries;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupValidator = $serviceGroupValidator;
        $this->serviceRepository = $serviceRepository;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function getOrCreateServiceGroupsPerLanguage(ServiceGroupModel $serviceGroup, array $languages): KeyValueDtoList
    {
        $serviceGroups = $this->serviceGroupRepository->getAllByKey($serviceGroup->key);
        $serviceGroupsPerLanguage = new KeyValueDtoList();
        $missingLanguages = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $serviceGroupModel = Searcher::findObject($serviceGroups, 'language', $languageCode);

            if (isset($serviceGroupModel->id) && $serviceGroupModel->id === $serviceGroup->id) {
                continue;
            }

            if ($serviceGroupModel !== null) {
                $serviceGroupsPerLanguage->add(new KeyValueDto($languageCode, (string) $serviceGroupModel->id));
            } else {
                $missingLanguages->add(new KeyValueDto($languageCode, $languageCode));
            }
        }

        // Create missing languages of the Service Group
        if (count($missingLanguages->list)) {
            $newServiceGroupsPerLanguage = $this->handleAdditionalLanguages(
                array_column($missingLanguages->list, 'key'),
                [
                    'description' => $serviceGroup->description,
                    'key' => $serviceGroup->key,
                    'name' => $serviceGroup->name,
                    'position' => (string) $serviceGroup->position,
                    'preSelected' => (string) $serviceGroup->preSelected,
                    'status' => (string) $serviceGroup->status,
                ],
            );

            foreach ($newServiceGroupsPerLanguage->list as $languageServiceGroupId) {
                $serviceGroupsPerLanguage->add(new KeyValueDto($languageServiceGroupId->key, (string) $languageServiceGroupId->value));
            }
        }

        return $serviceGroupsPerLanguage;
    }

    public function handleAdditionalLanguages(array $languages, array $postData): KeyValueDtoList
    {
        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            $languages,
            new KeyValueDtoList([
                new KeyValueDto('description', $postData['description']),
                new KeyValueDto('name', $postData['name']),
            ]),
        );

        $serviceGroupPerLanguageList = new KeyValueDtoList();

        foreach ($languages as $languageCode) {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;
            $description = $postData['description'];
            $name = $postData['name'];

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                $description = array_column($translation->translations->list, 'value', 'key')['description'] ?? $description;
                $name = array_column($translation->translations->list, 'value', 'key')['name'] ?? $name;
            }

            $postData['description'] = $description;
            $postData['id'] = '-1';
            $postData['name'] = $name;
            $serviceGroupId = $this->save(
                -1,
                $languageCode,
                $postData,
            );

            if ($serviceGroupId !== null) {
                $serviceGroupPerLanguageList->add(new KeyValueDto($languageCode, $serviceGroupId));
            }
        }

        return $serviceGroupPerLanguageList;
    }

    public function reset(): bool
    {
        // Todo: Test if language switch works
        $this->language->loadBlogLanguage();

        foreach ($this->serviceGroupDefaultEntries->getDefaultEntries() as $defaultServiceGroupModel) {
            $serviceGroupModel = $this->serviceGroupRepository->getByKey($defaultServiceGroupModel->key);

            if ($serviceGroupModel) {
                $defaultServiceGroupModel->id = $serviceGroupModel->id;

                $this->serviceGroupRepository->update($defaultServiceGroupModel);
            } else {
                $this->serviceGroupRepository->insert($defaultServiceGroupModel);
            }
        }

        $this->language->unloadBlogLanguage();

        return true;
    }

    public function save(int $id, string $languageCode, array $postData): ?int
    {
        if (!$this->serviceGroupValidator->isValid($postData, $languageCode)) {
            return null;
        }

        $postData = Sanitizer::requestData($postData);
        $postData = $this->wpFunction->applyFilter('borlabsCookie/serviceGroup/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->serviceGroupRepository->findById($id);
        }

        $newModel = new ServiceGroupModel();
        $newModel->id = $id;
        $newModel->description = $postData['description'];
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->name = $postData['name'];
        $newModel->position = (int) $postData['position'];
        $newModel->preSelected = (bool) $postData['preSelected'];
        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = $existingModel->undeletable ?? false;

        if ($newModel->id !== -1) {
            $this->serviceGroupRepository->update($newModel);
        } else {
            $newModel = $this->serviceGroupRepository->insert($newModel);
        }

        return $newModel->id;
    }
}
