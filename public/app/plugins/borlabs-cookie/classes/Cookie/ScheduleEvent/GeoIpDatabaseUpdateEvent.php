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

namespace Borlabs\Cookie\ScheduleEvent;

use Borlabs\Cookie\System\GeoIp\GeoIp;
use Exception;

final class GeoIpDatabaseUpdateEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'GeoIpDatabaseUpdate';

    private GeoIp $geoIp;

    public function __construct(GeoIp $geoIp)
    {
        $this->geoIp = $geoIp;
    }

    public function deregister(): void
    {
        wp_clear_scheduled_hook(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME);
    }

    public function register(): void
    {
        add_action(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME, [$this, 'run']);

        if (!wp_next_scheduled(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME)) {
            wp_schedule_event(time(), 'daily', ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME);
        }
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->geoIp->downloadGeoIpDatabase();
    }
}
