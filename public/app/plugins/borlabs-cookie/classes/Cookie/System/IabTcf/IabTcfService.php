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

namespace Borlabs\Cookie\System\IabTcf;

use Borlabs\Cookie\ApiClient\IabTcfApiClient;
use Borlabs\Cookie\Dto\IabTcf\ConsentParameterDto;
use Borlabs\Cookie\Dto\IabTcf\DataCategoryDto;
use Borlabs\Cookie\Dto\IabTcf\DataRetentionDto;
use Borlabs\Cookie\Dto\IabTcf\IabTcfTranslationDto;
use Borlabs\Cookie\Dto\IabTcf\VendorUrlsDto;
use Borlabs\Cookie\Dto\System\ExternalFileDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\IabTcf\ConsentParameterDtoList;
use Borlabs\Cookie\DtoList\IabTcf\DataCategoryDtoList;
use Borlabs\Cookie\DtoList\IabTcf\VendorUrlsDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\IabTcf\VendorModel;
use Borlabs\Cookie\Repository\IabTcf\VendorRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Option\Option;

final class IabTcfService
{
    public const OPTION_GOBAL_VENDOR_LIST_LAST_API_CHECK = 'GlobalVendorListLastUpdate';

    public const VENDOR_LIST = 'vendor-list.json';

    private FileManager $fileManager;

    private GeneralConfig $generalConfig;

    private IabTcfApiClient $iabTcfApiClient;

    private IabTcfConfig $iabTcfConfig;

    private License $license;

    private Option $option;

    private VendorRepository $vendorRepository;

    public function __construct(
        FileManager $fileManager,
        GeneralConfig $generalConfig,
        IabTcfApiClient $apiClient,
        IabTcfConfig $iabTcfConfig,
        License $license,
        Option $option,
        VendorRepository $vendorRepository
    ) {
        $this->fileManager = $fileManager;
        $this->generalConfig = $generalConfig;
        $this->iabTcfApiClient = $apiClient;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->license = $license;
        $this->option = $option;
        $this->vendorRepository = $vendorRepository;
    }

    public function getLastSuccessfulCheckWithApiTimestamp(): ?int
    {
        $option = $this->option->getGlobal(self::OPTION_GOBAL_VENDOR_LIST_LAST_API_CHECK);

        return $option->value !== false ? (int) ($option->value) : null;
    }

    public function getTranslations(string $languageCode): IabTcfTranslationDto
    {
        $iabTcfTranslationDto = new IabTcfTranslationDto();

        $fileContent = null;

        if ($languageCode !== 'en') {
            $fileContent = $this->getPurposeFileContent($languageCode);
        }

        if ($fileContent === null) {
            $fileContent = $this->getVendorListFileContent();
        }

        $data = $fileContent ? json_decode($fileContent, true) : null;
        $iabTcfTranslationDto->dataCategories = new DataCategoryDtoList(
            array_map(
                static fn ($data) => new DataCategoryDto(
                    (int) $data['id'],
                    trim($data['name']),
                    trim($data['description']),
                ),
                $data['dataCategories'] ?? [],
            ),
        );
        $iabTcfTranslationDto->features = $this->buildConsentParameterDtoList($data['features'] ?? []);
        $iabTcfTranslationDto->purposes = $this->buildConsentParameterDtoList($data['purposes'] ?? []);
        $iabTcfTranslationDto->specialFeatures = $this->buildConsentParameterDtoList($data['specialFeatures'] ?? []);
        $iabTcfTranslationDto->specialPurposes = $this->buildConsentParameterDtoList($data['specialPurposes'] ?? []);

        return $iabTcfTranslationDto;
    }

    public function getVendorsFromVendorList(): ?array
    {
        $fileContent = $this->getVendorListFileContent();
        $data = $fileContent ? json_decode($fileContent, true) : null;

        if (!isset($data['vendors'])) {
            return null;
        }

        $data['vendors'] = array_filter($data['vendors'], function ($vendor) {
            return !isset($vendor['deletedDate']);
        });

        return $data['vendors'] ?? null;
    }

    public function isGlobalVendorListDownloaded(): bool
    {
        return $this->fileManager->getGloballyStoredFile(self::VENDOR_LIST) !== null;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function updateGlobalVendorListFile(): void
    {
        if ($this->license->get() === null) {
            return;
        }

        $globalVendorListAttachment = $this->iabTcfApiClient->requestGlobalVendorListAttachmentData();
        $globalVendorListFile = $this->fileManager->storeExternalFileGlobally(
            new ExternalFileDto($globalVendorListAttachment->downloadUrl),
            self::VENDOR_LIST,
        );

        if ($globalVendorListFile !== null) {
            $this->option->setGlobal(self::OPTION_GOBAL_VENDOR_LIST_LAST_API_CHECK, time());
        }
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function updatePurposeTranslationFiles(): void
    {
        // TODO: do not use this, use language adapter to get list of configured languages
        $borlabsCookieConfigs = $this->generalConfig->getAllConfigs();

        foreach ($borlabsCookieConfigs as $optionData) {
            if ($optionData->language === 'en') {
                // The default language is English, so there is no translation file.
                continue;
            }

            $purposeTranslationAttachment = $this->iabTcfApiClient->requestPurposeTranslationAttachmentData($optionData->language);
            $this->fileManager->storeExternalFileGlobally(
                new ExternalFileDto($purposeTranslationAttachment->downloadUrl),
                'purposes-' . $optionData->language . '.json',
            );
        }
    }

    public function updateVendorConfiguration(string $languageCode): bool
    {
        $iabTcfConfig = $this->iabTcfConfig->get();
        $iabTcfConfig->vendors = new KeyValueDtoList();
        $activeVendors = $this->vendorRepository->getAllActive();

        foreach ($activeVendors as $vendor) {
            $iabTcfConfig->vendors->add(new KeyValueDto((string) $vendor->vendorId, (string) $vendor->vendorId));
        }

        return $this->iabTcfConfig->save($iabTcfConfig, $languageCode);
    }

    public function updateVendors(): bool
    {
        $vendorObject = [];
        $vendors = $this->vendorRepository->getAll();
        $vendorListFileContent = $this->getVendorListFileContent();

        if ($vendorListFileContent !== null) {
            $vendorObject = json_decode($vendorListFileContent, true);
        }

        if (isset($vendorObject['vendors'])) {
            foreach ($vendorObject['vendors'] as $vendorData) {
                $vendorModel = new VendorModel();
                $vendorModel->vendorId = $vendorData['id'];
                $vendorModel->cookieMaxAgeSeconds = (int) ($vendorData['cookieMaxAgeSeconds'] ?? 0);
                $vendorModel->dataDeclaration = $vendorData['dataDeclaration'] ?? [];
                $vendorModel->dataRetention = new DataRetentionDto(
                    (int) ($vendorData['dataRetention']['stdRetention'] ?? 0),
                    new KeyValueDtoList(
                        array_map(
                            static fn ($purpose, $retention) => new KeyValueDto((string) $purpose, (int) $retention),
                            array_keys($vendorData['dataRetention']['purposes'] ?? []),
                            array_values($vendorData['dataRetention']['purposes'] ?? []),
                        ),
                    ),
                    new KeyValueDtoList(
                        array_map(
                            static fn ($specialPurpose, $retention) => new KeyValueDto((string) $specialPurpose, (int) $retention),
                            array_keys($vendorData['dataRetention']['specialPurposes'] ?? []),
                            array_values($vendorData['dataRetention']['specialPurposes'] ?? []),
                        ),
                    ),
                );
                $vendorModel->deviceStorageDisclosureUrl = $vendorData['deviceStorageDisclosureUrl'] ?? '';
                $vendorModel->features = $vendorData['features'] ?? [];
                $vendorModel->legIntPurposes = $vendorData['legIntPurposes'] ?? [];
                $vendorModel->name = $vendorData['name'];
                $vendorModel->purposes = $vendorData['purposes'] ?? [];
                $vendorModel->specialFeatures = $vendorData['specialFeatures'] ?? [];
                $vendorModel->specialPurposes = $vendorData['specialPurposes'] ?? [];
                $vendorModel->urls = new VendorUrlsDtoList(
                    array_map(
                        static fn ($urlData) => new VendorUrlsDto(
                            $urlData['langId'],
                            $urlData['legIntClaim'] ?? '',
                            $urlData['privacy'] ?? '',
                        ),
                        $vendorData['urls'] ?? [],
                    ),
                );
                $vendorModel->usesCookies = (bool) ($vendorData['usesCookies'] ?? false);
                $vendorModel->usesNonCookieAccess = (bool) ($vendorData['usesNonCookieAccess'] ?? false);

                $existingModel = Searcher::findObject($vendors, 'vendorId', (string) $vendorModel->vendorId, false);

                if ($existingModel) {
                    $vendorModel->id = $existingModel->id;
                    $vendorModel->status = $existingModel->status;
                }

                if (isset($vendorData['deletedDate'])) {
                    if ($vendorModel->id !== -1) {
                        $this->vendorRepository->delete($vendorModel);
                    }

                    continue;
                }

                if ($vendorModel->id !== -1) {
                    // Only update when something has changed
                    if ($vendorModel != $existingModel) {
                        $this->vendorRepository->update($vendorModel);
                    }
                } else {
                    $this->vendorRepository->insert($vendorModel);
                }
            }

            return true;
        }

        return false;
    }

    private function buildConsentParameterDtoList(array $purposes): ConsentParameterDtoList
    {
        return new ConsentParameterDtoList(
            array_map(
                static fn ($data) => new ConsentParameterDto(
                    (int) $data['id'],
                    trim($data['name']),
                    trim($data['description']),
                    array_map(static fn ($text) => trim($text), $data['illustrations']),
                ),
                $purposes,
            ),
        );
    }

    private function getPurposeFileContent(string $languageCode): ?string
    {
        return $this->fileManager->getGloballyStoredFileContent('purposes-' . $languageCode . '.json');
    }

    private function getVendorListFileContent(): ?string
    {
        return $this->fileManager->getGloballyStoredFileContent(self::VENDOR_LIST);
    }
}
