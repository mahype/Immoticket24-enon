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

use Borlabs\Cookie\System\Consent\ConsentLogService;
use Borlabs\Cookie\System\Language\Language;
use Exception;
use WP_REST_Request;

/**
 * Handles returning of the consent log history.
 */
final class ConsentHistoryEndpoint implements RestEndpointInterface
{
    private ConsentLogService $consentLogService;

    private Language $language;

    public function __construct(ConsentLogService $consentLogService, Language $language)
    {
        $this->consentLogService = $consentLogService;
        $this->language = $language;
    }

    public function history(WP_REST_Request $request): array
    {
        $uid = null;

        if (isset($_COOKIE['borlabs-cookie'])) {
            try {
                $pluginCookie = json_decode(stripslashes($_COOKIE['borlabs-cookie']));

                if (isset($pluginCookie->uid)) {
                    $uid = $pluginCookie->uid;
                }
            } catch (Exception $e) {
                // Nothing
            }
        }

        if (isset($uid)) {
            $language = $request->get_param('language') ?? $this->language->getCurrentLanguageCode();

            if (!$this->language->isValidLanguageCode($language)) {
                return [];
            }

            return $this->consentLogService->getHistory($uid);
        }

        return [];
    }

    public function register(): void
    {
        register_rest_route(
            RestEndpointManager::NAMESPACE . '/v1',
            '/consent/history',
            [
                'methods' => 'GET',
                'callback' => [$this, 'history'],
                'permission_callback' => '__return_true',
            ],
        );
    }
}
