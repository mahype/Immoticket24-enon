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

namespace Borlabs\Cookie\System\Package\PackageManagerComponent;

use Borlabs\Cookie\ApiClient\Transformer\StyleBlockerTransformer;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;
use Borlabs\Cookie\System\Log\Log;

final class StyleBlockerComponent
{
    private Log $log;

    private StyleBlockerRepository $styleBlockerRepository;

    private StyleBlockerTransformer $styleBlockerTransformer;

    public function __construct(
        Log $log,
        StyleBlockerRepository $styleBlockerRepository,
        StyleBlockerTransformer $styleBlockerTransformer
    ) {
        $this->log = $log;
        $this->styleBlockerRepository = $styleBlockerRepository;
        $this->styleBlockerTransformer = $styleBlockerTransformer;
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<PackageModel>
     */
    public function checkUsage(StyleBlockerModel $styleBlockerModel, PackageModel $ignorePackage, array $packages): array
    {
        $resourcesInUse = [];

        foreach ($packages as $package) {
            if ($package->installedAt === null || $package->id === $ignorePackage->id) {
                continue;
            }

            foreach ($package->components->styleBlockers->list as $styleBlocker) {
                if ($styleBlocker->key === $styleBlockerModel->key) {
                    $resourcesInUse[] = $package;

                    break;
                }
            }
        }

        return $resourcesInUse;
    }

    public function install(object $styleBlockerData, string $borlabsServicePackageKey): InstallationStatusDto
    {
        $styleBlockerModel = $this->styleBlockerTransformer->toModel($styleBlockerData, $borlabsServicePackageKey);
        $styleBlocker = $this->styleBlockerRepository->getByKey($styleBlockerData->key);

        if ($styleBlocker !== null) {
            $styleBlockerModel->id = $styleBlocker->id;
            $this->styleBlockerRepository->update($styleBlockerModel);
        } else {
            $styleBlockerModel = $this->styleBlockerRepository->insert($styleBlockerModel);
        }

        if ($styleBlockerModel->id === -1) {
            $this->log->error(
                'Style Blocker "{{ styleBlockerData.name }}" could not be installed.',
                [
                    'packageKey' => $borlabsServicePackageKey,
                    'styleBlockerData' => $styleBlockerData,
                ],
            );
        }

        return new InstallationStatusDto(
            $styleBlockerModel->id !== -1 ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::SCRIPT_BLOCKER(),
            $styleBlockerModel->key,
            $styleBlockerModel->name,
            $styleBlockerModel->id,
        );
    }

    public function reassignToOtherPackage(PackageModel $packageModel, StyleBlockerModel $styleBlockerModel): InstallationStatusDto
    {
        $styleBlockerModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->styleBlockerRepository->update($styleBlockerModel);

        if ($success) {
            return new InstallationStatusDto(
                InstallationStatusEnum::SUCCESS(),
                ComponentTypeEnum::STYLE_BLOCKER(),
                $styleBlockerModel->key,
                $styleBlockerModel->name,
                $styleBlockerModel->id,
            );
        }

        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::STYLE_BLOCKER(),
            $styleBlockerModel->key,
            $styleBlockerModel->name,
            $styleBlockerModel->id,
        );
    }

    public function uninstall(
        PackageModel $packageModel,
        StyleBlockerModel $styleBlockerModel,
        array $packages
    ): InstallationStatusDto {
        $usage = $this->checkUsage($styleBlockerModel, $packageModel, $packages);

        if (count($usage) === 0) {
            $result = $this->styleBlockerRepository->forceDelete($styleBlockerModel);

            if ($result !== 1) {
                return new InstallationStatusDto(
                    InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::STYLE_BLOCKER(),
                    $styleBlockerModel->key,
                    $styleBlockerModel->name,
                    $styleBlockerModel->id,
                );
            }

            return new InstallationStatusDto(
                InstallationStatusEnum::SUCCESS(),
                ComponentTypeEnum::STYLE_BLOCKER(),
                $styleBlockerModel->key,
                $styleBlockerModel->name,
                $styleBlockerModel->id,
            );
        }

        return $this->reassignToOtherPackage($usage[0], $styleBlockerModel);
    }
}
