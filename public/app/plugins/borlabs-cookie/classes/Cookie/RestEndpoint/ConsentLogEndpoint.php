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

use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Consent\ConsentLogService;
use WP_REST_Request;

/**
 * Handles logging of the consent log history.
 */
final class ConsentLogEndpoint implements RestEndpointInterface
{
    private ConsentLogService $consentLogService;

    public function __construct(ConsentLogService $consentLogService)
    {
        $this->consentLogService = $consentLogService;
    }

    public function log(WP_REST_Request $request): array
    {
        $requestData = Transformer::buildNestedArray($request->get_params());

        if (!isset($requestData['language'],
            $requestData['consentLog']['uid'],
            $requestData['consentLog']['version'],
            $requestData['consentLog']['borlabsCookieConsentString'],
            $requestData['consentLog']['iabTcfTCString'])
        ) {
            return ['success' => false];
        }

        $this->consentLogService->add(
            $requestData['language'],
            $requestData['consentLog']['uid'],
            (int) $requestData['consentLog']['version'],
            $requestData['consentLog']['borlabsCookieConsentString'],
            $requestData['consentLog']['iabTcfTCString'],
        );

        return ['success' => true];
    }

    public function register(): void
    {
        register_rest_route(
            RestEndpointManager::NAMESPACE . '/v1',
            '/consent/log',
            [
                'methods' => 'POST',
                'callback' => [$this, 'log'],
                'permission_callback' => '__return_true',
            ],
        );
    }
}
