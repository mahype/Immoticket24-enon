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

use Borlabs\Cookie\Repository\Service\ServiceRepository;

class FallbackCodeManager
{
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getFallbackCodes(): string
    {
        $services = $this->serviceRepository->getAllOfCurrentLanguage(false, true);
        $return = '';

        foreach ($services as $service) {
            if ($service->fallbackCode !== '') {
                $search = array_map(static fn ($value) => '{{ ' . $value . ' }}', array_column($service->settingsFields->list, 'key'));
                $replace = array_column($service->settingsFields->list, 'value');
                $return .= str_replace($search, $replace, $service->fallbackCode);
            }
        }

        return $return;
    }
}
