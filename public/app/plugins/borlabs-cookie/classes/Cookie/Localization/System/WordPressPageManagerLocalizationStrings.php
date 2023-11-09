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

namespace Borlabs\Cookie\Localization\System;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class WordPressPageManagerLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // MenuTitle
            'menuTitle' => [
                'borlabsCookie' => _x(
                    'Borlabs Cookie',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    '- Compatibility Patches',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'consentLogs' => _x(
                    '- Consent Logs',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'consentManagement' => _x(
                    'Consent Management',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlocker' => _x(
                    'Content Blocker',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockerAppearance' => _x(
                    '- Appearance',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockerManage' => _x(
                    '- Manage',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockerSettings' => _x(
                    '- Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dashboard' => _x(
                    'Dashboard',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialog' => _x(
                    'Dialog',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialogAppearance' => _x(
                    '- Appearance',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialogLocalization' => _x(
                    '- Localization',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    '- Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcf' => _x(
                    '- IAB TCF',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfManageVendors' => _x(
                    '- - Manage Vendors',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfSettings' => _x(
                    '- - Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'library' => _x(
                    'Library',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'license' => _x(
                    '- License',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'provider' => _x(
                    '- Provider',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'scanner' => _x(
                    'Scanner',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'scriptBlocker' => _x(
                    'Script Blocker',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    '- Service Groups',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    '- Services',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'settings' => _x(
                    'Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'styleBlocker' => _x(
                    'Style Blocker',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'system' => _x(
                    'System',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'widget' => _x(
                    'Widget',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
            ],

            // SiteTitle
            'siteTitle' => [
                'borlabsCookie' => _x(
                    'Borlabs Cookie',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    'Compatibility Patches',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'consentLogs' => _x(
                    'Consent Logs',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'consentManagement' => _x(
                    'Consent Management',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlocker' => _x(
                    'Content Blocker',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockerAppearance' => _x(
                    'Content Blocker - Appearance',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockerManage' => _x(
                    'Content Blocker - Manage',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockerSettings' => _x(
                    'Content Blocker - Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dashboard' => _x(
                    'Dashboard',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialog' => _x(
                    'Dialog',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialogAppearance' => _x(
                    'Dialog - Appearance',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialogLocalization' => _x(
                    'Dialog - Localization',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    'Dialog - Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'iabTcf' => _x(
                    'IAB TCF',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfManageVendors' => _x(
                    'IAB TCF Manage Vendors',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfSettings' => _x(
                    'IAB TCF Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'library' => _x(
                    'Library',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'license' => _x(
                    'License',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'provider' => _x(
                    'Provider',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'scanner' => _x(
                    'Scanner',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'scriptBlocker' => _x(
                    'Script Blocker',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    'Service Groups',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    'Services',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'settings' => _x(
                    'Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'styleBlocker' => _x(
                    'Style Blocker',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'system' => _x(
                    'System',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'widget' => _x(
                    'Widget',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
