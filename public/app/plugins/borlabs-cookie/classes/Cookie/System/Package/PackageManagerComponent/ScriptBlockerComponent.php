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

use Borlabs\Cookie\ApiClient\Transformer\ScriptBlockerTransformer;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\System\Log\Log;

final class ScriptBlockerComponent
{
    private Log $log;

    private ScriptBlockerRepository $scriptBlockerRepository;

    private ScriptBlockerTransformer $scriptBlockerTransformer;

    public function __construct(
        Log $log,
        ScriptBlockerRepository $scriptBlockerRepository,
        ScriptBlockerTransformer $scriptBlockerTransformer
    ) {
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->scriptBlockerTransformer = $scriptBlockerTransformer;
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<PackageModel>
     */
    public function checkUsage(ScriptBlockerModel $scriptBlockerModel, PackageModel $ignorePackage, array $packages): array
    {
        $resourcesInUse = [];

        foreach ($packages as $package) {
            if ($package->installedAt === null || $package->id === $ignorePackage->id) {
                continue;
            }

            foreach ($package->components->scriptBlockers->list as $scriptBlocker) {
                if ($scriptBlocker->key === $scriptBlockerModel->key) {
                    $resourcesInUse[] = $package;

                    break;
                }
            }
        }

        return $resourcesInUse;
    }

    public function install(object $scriptBlockerData, string $borlabsServicePackageKey): InstallationStatusDto
    {
        $scriptBlockerModel = $this->scriptBlockerTransformer->toModel($scriptBlockerData, $borlabsServicePackageKey);
        $scriptBlocker = $this->scriptBlockerRepository->getByKey($scriptBlockerData->key);

        if ($scriptBlocker !== null) {
            $scriptBlockerModel->id = $scriptBlocker->id;
            $this->scriptBlockerRepository->update($scriptBlockerModel);
        } else {
            $scriptBlockerModel = $this->scriptBlockerRepository->insert($scriptBlockerModel);
        }

        if ($scriptBlockerModel->id === -1) {
            $this->log->error(
                'Script Blocker "{{ $scriptBlockerData.name }}" could not be installed.',
                [
                    'packageKey' => $borlabsServicePackageKey,
                    'scriptBlockerData' => $scriptBlockerData,
                ],
            );
        }

        return new InstallationStatusDto(
            $scriptBlockerModel->id !== -1 ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::SCRIPT_BLOCKER(),
            $scriptBlockerModel->key,
            $scriptBlockerModel->name,
            $scriptBlockerModel->id,
        );
    }

    public function reassignToOtherPackage(PackageModel $packageModel, ScriptBlockerModel $scriptBlockerModel): InstallationStatusDto
    {
        $scriptBlockerModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->scriptBlockerRepository->update($scriptBlockerModel);

        if ($success) {
            return new InstallationStatusDto(
                InstallationStatusEnum::SUCCESS(),
                ComponentTypeEnum::SCRIPT_BLOCKER(),
                $scriptBlockerModel->key,
                $scriptBlockerModel->name,
                $scriptBlockerModel->id,
            );
        }

        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::SCRIPT_BLOCKER(),
            $scriptBlockerModel->key,
            $scriptBlockerModel->name,
            $scriptBlockerModel->id,
        );
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function uninstall(PackageModel $packageModel, ScriptBlockerModel $scriptBlockerModel, array $packages): InstallationStatusDto
    {
        $usage = $this->checkUsage($scriptBlockerModel, $packageModel, $packages);

        if (count($usage) === 0) {
            $result = $this->scriptBlockerRepository->forceDelete($scriptBlockerModel);

            if ($result !== 1) {
                return new InstallationStatusDto(
                    InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::SCRIPT_BLOCKER(),
                    $scriptBlockerModel->key,
                    $scriptBlockerModel->name,
                    $scriptBlockerModel->id,
                );
            }

            return new InstallationStatusDto(
                InstallationStatusEnum::SUCCESS(),
                ComponentTypeEnum::SCRIPT_BLOCKER(),
                $scriptBlockerModel->key,
                $scriptBlockerModel->name,
                $scriptBlockerModel->id,
            );
        }

        return $this->reassignToOtherPackage(
            $usage[0],
            $scriptBlockerModel,
        );
    }
}
