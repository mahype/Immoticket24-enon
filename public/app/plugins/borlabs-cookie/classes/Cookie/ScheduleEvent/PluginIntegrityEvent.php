<?php
/*
 *  Copyright (c) 2024 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\ScheduleEvent;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;

final class PluginIntegrityEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'PluginIntegrity';

    private Container $container;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        Container $container,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->container = $container;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function deregister(): void
    {
        $this->wpFunction->wpClearScheduledHook(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME);
    }

    public function register(): void
    {
        $this->wpFunction->addAction(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME, [$this, 'run']);

        if (!$this->wpFunction->wpNextScheduled(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME)) {
            $this->wpFunction->wpScheduleEvent(
                time(),
                'hourly',
                ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME,
            );
        }
    }

    public function run(): void
    {
        $prefix = $this->wpdb->prefix;
        $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->run($prefix);
    }
}
