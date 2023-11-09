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

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Service\ServiceCookieValidator;

class ServiceCookieService
{
    private Language $language;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceCookieValidator $serviceCookieValidator;

    private ServiceRepository $serviceRepository;

    private TranslatorService $translatorService;

    public function __construct(
        Language $language,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceCookieValidator $serviceCookieValidator,
        ServiceRepository $serviceRepository,
        TranslatorService $translatorService
    ) {
        $this->language = $language;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceCookieValidator = $serviceCookieValidator;
        $this->serviceRepository = $serviceRepository;
        $this->translatorService = $translatorService;
    }

    public function handleAdditionalLanguages(
        array $languages,
        array $postData,
        KeyValueDtoList $servicePerLanguageList
    ): void {
        $sourceTexts = new KeyValueDtoList();

        foreach ($postData as $index => $cookieData) {
            $sourceTexts->add(new KeyValueDto($index . '_description', $cookieData['description']));
            $sourceTexts->add(new KeyValueDto($index . '_lifetime', $cookieData['lifetime']));
        }

        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            $languages,
            $sourceTexts,
        );

        foreach ($languages as $languageCode) {
            $serviceId = Searcher::findObject($servicePerLanguageList->list, 'key', $languageCode)->value ?? null;
            $service = $this->serviceRepository->findById((int) $serviceId, ['serviceCookies']);

            if (!isset($service)) {
                continue;
            }

            foreach ($postData as $index => $cookieData) {
                $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;
                $description = $cookieData['description'];
                $lifetime = $cookieData['lifetime'];

                if (
                    isset($translation->translations->list)
                    && is_array($translation->translations->list)
                    && count($translation->translations->list)
                ) {
                    $description = array_column($translation->translations->list, 'value', 'key')[$index . '_description'] ?? $description;
                    $lifetime = array_column($translation->translations->list, 'value', 'key')[$index . '_lifetime'] ?? $lifetime;
                }

                $postData[$index]['description'] = $description;
                $postData[$index]['lifetime'] = $lifetime;
            }

            $this->save($service, $postData);
        }
    }

    public function save(ServiceModel $serviceModel, array $postData): void
    {
        if (isset($serviceModel->serviceCookies)) {
            foreach ($serviceModel->serviceCookies as $serviceCookie) {
                $this->serviceCookieRepository->delete($serviceCookie);
            }
        }

        foreach ($postData as $newServiceCookieData) {
            if (!$this->serviceCookieValidator->isValid($newServiceCookieData)) {
                continue;
            }

            $newServiceCookieData = Sanitizer::requestData($newServiceCookieData);

            $newModel = new ServiceCookieModel();
            $newModel->serviceId = $serviceModel->id;
            $newModel->description = $newServiceCookieData['description'];
            $newModel->hostname = $newServiceCookieData['hostname'];
            $newModel->lifetime = $newServiceCookieData['lifetime'];
            $newModel->name = $newServiceCookieData['name'];
            $newModel->path = $newServiceCookieData['path'];
            $newModel->purpose = CookiePurposeEnum::fromValue($newServiceCookieData['purpose']);
            $newModel->type = CookieTypeEnum::fromValue($newServiceCookieData['type']);
            $this->serviceCookieRepository->insert($newModel);
        }
    }
}
