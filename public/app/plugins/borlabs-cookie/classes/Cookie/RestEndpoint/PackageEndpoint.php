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

namespace Borlabs\Cookie\RestEndpoint;

use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\System\Package\PackageManager;
use WP_REST_Request;

final class PackageEndpoint implements RestEndpointInterface
{
    private PackageManager $packageManager;

    private PackageRepository $packageRepository;

    public function __construct(PackageManager $packageManager, PackageRepository $packageRepository)
    {
        $this->packageManager = $packageManager;
        $this->packageRepository = $packageRepository;
    }

    public function install(WP_REST_Request $request): ?InstallationStatusDtoList
    {
        $package = null;

        if ($request->get_param('id')) {
            $package = $this->packageRepository->findById((int) $request->get_param('id'), [
                'services',
                'contentBlockers',
                'compatibilityPatches',
                'scriptBlockers',
                'styleBlockers',
            ]);
        }

        if ($package !== null) {
            return $this->packageManager->install(
                $package,
                $request->get_body_params(),
            );
        }

        return null;
    }

    public function register(): void
    {
        register_rest_route(RestEndpointManager::NAMESPACE . '/v1', '/package/install/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'install'],
            'args' => [
                'id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
            ],
            'permission_callback' => function () {
                return current_user_can('manage_borlabs_cookie');
            },
        ]);
    }
}
