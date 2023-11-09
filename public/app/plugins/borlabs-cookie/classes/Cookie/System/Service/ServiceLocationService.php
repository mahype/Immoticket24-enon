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

use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Validator\Service\ServiceLocationValidator;

class ServiceLocationService
{
    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceLocationValidator $serviceLocationValidator;

    public function __construct(
        ServiceLocationRepository $serviceLocationRepository,
        ServiceLocationValidator $serviceLocationValidator
    ) {
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceLocationValidator = $serviceLocationValidator;
    }

    public function save(ServiceModel $serviceModel, array $postData): void
    {
        if (isset($serviceModel->serviceLocations)) {
            foreach ($serviceModel->serviceLocations as $serviceLocation) {
                $this->serviceLocationRepository->delete($serviceLocation);
            }
        }

        foreach ($postData as $newServiceLocationData) {
            if (!$this->serviceLocationValidator->isValid($newServiceLocationData)) {
                continue;
            }

            $newServiceLocationData = Sanitizer::requestData($newServiceLocationData);

            $newModel = new ServiceLocationModel();
            $newModel->serviceId = $serviceModel->id;
            $newModel->hostname = $newServiceLocationData['hostname'];
            $newModel->path = $newServiceLocationData['path'];
            $this->serviceLocationRepository->insert($newModel);
        }
    }
}
