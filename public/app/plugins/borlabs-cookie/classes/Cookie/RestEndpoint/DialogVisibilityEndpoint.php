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

use Borlabs\Cookie\System\GeoIp\GeoIp;
use Borlabs\Cookie\System\Language\Language;
use WP_REST_Request;

/**
 * Handles the visibility of the cookie box in different regions.
 */
class DialogVisibilityEndpoint implements RestEndpointInterface
{
    private GeoIp $geoIp;

    private Language $language;

    /**
     * CookieBoxEndpoint constructor.
     */
    public function __construct(GeoIp $geoIp, Language $language)
    {
        $this->geoIp = $geoIp;
        $this->language = $language;
    }

    /**
     * Registers the REST endpoints.
     */
    public function register(): void
    {
        register_rest_route(
            RestEndpointManager::NAMESPACE . '/v1',
            '/dialog/visibility',
            [
                'methods' => 'GET',
                'callback' => [$this, 'visibility',],
                'permission_callback' => '__return_true',
            ],
        );
    }

    /**
     * Visibility of the cookie box in the country of the IP address of the user.
     */
    public function visibility(WP_REST_Request $request): array
    {
        $language = $request->get_param('language') ?? $this->language->getCurrentLanguageCode();

        if (!$this->language->isValidLanguageCode($language)) {
            // Fallback, always display dialog if language is invalid
            return ['visible' => true];
        }
        $showDialogForCurrentUser = $this->geoIp->getShowDialogStatusForCurrentUser($language);

        return [
            'visible' => $showDialogForCurrentUser,
        ];
    }
}
