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

use WP_REST_Request;

final class HelperEndpoint implements RestEndpointInterface
{
    public function __construct()
    {
    }

    public function getPermalinkById(WP_REST_Request $request): array
    {
        $postId = $request->get_param('post_id');

        return ['permalink' => get_permalink($postId)];
    }

    public function register(): void
    {
        register_rest_route(RestEndpointManager::NAMESPACE . '/v1', '/helper/permalink/(?P<post_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'getPermalinkById'],
            'args' => [
                'post_id' => [
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
