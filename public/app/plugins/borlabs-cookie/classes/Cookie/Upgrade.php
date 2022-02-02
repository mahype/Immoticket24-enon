<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                      developed by Borlabs
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2021 Borlabs - Benjamin A. Bornschein. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs - Benjamin A. Bornschein, https://borlabs.io
 * @author Benjamin A. Bornschein, Borlabs ben@borlabs.io
 *
 */

namespace BorlabsCookie\Cookie;

use BorlabsCookie\Cookie\Backend\CSS;
use BorlabsCookie\Cookie\Frontend\Services\Ezoic;
use BorlabsCookie\Cookie\Frontend\Services\EzoicMarketing;
use BorlabsCookie\Cookie\Frontend\Services\EzoicPreferences;
use BorlabsCookie\Cookie\Frontend\Services\EzoicStatistics;

class Upgrade
{

    private static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private $currentBlogId = '';
    private $versionUpgrades
        = [
            'upgradeVersion_2_1_0' => '2.1.0',
            'upgradeVersion_2_1_8' => '2.1.8',
            'upgradeVersion_2_1_9' => '2.1.9',
            'upgradeVersion_2_1_13' => '2.1.13',
            'upgradeVersion_2_2_0' => '2.2.0',
            'upgradeVersion_2_2_2' => '2.2.2',
            'upgradeVersion_2_2_3' => '2.2.3',
            'upgradeVersion_2_2_6' => '2.2.6',
            'upgradeVersion_2_2_9' => '2.2.9',
            'upgradeVersion_2_2_29' => '2.2.29',
            'upgradeVersion_2_2_43' => '2.2.43',
            'upgradeVersion_2_2_44' => '2.2.44',
            'upgradeVersion_2_2_45' => '2.2.45',
        ];

    public function __construct()
    {
    }

    public function __clone()
    {
        trigger_error('Cloning is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserialize is forbidden.', E_USER_ERROR);
    }

    /**
     * clearCache function.
     *
     * @access public
     * @return void
     */
    public function clearCache()
    {
        Log::getInstance()->info(__METHOD__, 'Clear cache after upgrade');

        // Autoptimize
        if (class_exists('\autoptimizeCache')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of Autoptimize');

            \autoptimizeCache::clearall();
        }

        // Borlabs Cache
        if (class_exists('\Borlabs\Cache\Frontend\Garbage')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of Borlabs Cache');

            \Borlabs\Cache\Frontend\Garbage::getInstance()->clearStylesPreCacheFiles();

            \Borlabs\Cache\Frontend\Garbage::getInstance()->clearCache();
        }

        // WP Fastest Cache
        if (function_exists('wpfc_clear_all_cache')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of WP Fastest Cache');

            wpfc_clear_all_cache(true);
        }

        // WP Rocket
        if (function_exists('rocket_clean_domain')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of WP Rocket');

            rocket_clean_domain();
        }

        // WP Super Cache
        if (function_exists('wp_cache_clean_cache')) {
            global $file_prefix;

            if (isset($file_prefix)) {
                Log::getInstance()->info(__METHOD__, 'Clear cache of WP Super Cache');

                wp_cache_clean_cache($file_prefix);
            }
        }

        update_option('BorlabsCookieClearCache', false, 'no');

        Log::getInstance()->info(__METHOD__, 'Cache cleared');
    }

    /**
     * getVersionUpgrades function.
     *
     * @access public
     * @return void
     */
    public function getVersionUpgrades()
    {
        return $this->versionUpgrades;
    }

    public function upgradeVersion_2_1_0()
    {
        global $wpdb;

        // Update tables
        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';
        $tableNameCookieGroups = $wpdb->prefix . 'borlabs_cookie_groups'; // ->prefix contains base_prefix + blog id
        $tableNameContentBlocker = $wpdb->prefix
            . 'borlabs_cookie_content_blocker'; // ->prefix contains base_prefix + blog id

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("ALTER TABLE `" . $tableNameCookies . "` MODIFY `language` varchar(16);");
        }

        if (Install::getInstance()->checkIfTableExists($tableNameCookieGroups)) {
            $wpdb->query("ALTER TABLE `" . $tableNameCookieGroups . "` MODIFY `language` varchar(16);");
        }

        if (Install::getInstance()->checkIfTableExists($tableNameContentBlocker)) {
            $wpdb->query("ALTER TABLE `" . $tableNameContentBlocker . "` MODIFY `language` varchar(16);");
        }

        // Add new table
        $charsetCollate = $wpdb->get_charset_collate();
        $tableNameScriptBlocker = $wpdb->prefix
            . 'borlabs_cookie_script_blocker'; // ->prefix contains base_prefix + blog id

        $sqlCreateTableScriptBlocker = Install::getInstance()
            ->getCreateTableStatementScriptBlocker($tableNameScriptBlocker,
                $charsetCollate
            );

        $wpdb->query($sqlCreateTableScriptBlocker);

        // Add user capabilities
        Install::getInstance()->addUserCapabilities();

        update_option('BorlabsCookieVersion', '2.1.0', 'no');
    }

    public function upgradeVersion_2_1_13()
    {
        global $wpdb;

        // Change cookie log table
        $tableName = $wpdb->prefix . 'borlabs_cookie_consent_log';

        if (Install::getInstance()->checkIfTableExists($tableName)) {
            // Check if key exists
            $checkOldKey = $wpdb->query("
                SHOW
                    INDEXES
                FROM
                    `" . $tableName . "`
                WHERE
                    `Key_name` = 'is_latest'
            "
            );

            if ($checkOldKey) {
                // Remove key
                $wpdb->query("
                    ALTER TABLE
                        `" . $tableName . "`
                    DROP INDEX
                        `is_latest`
                "
                );
            }

            // Add new key
            $checkNewKey = $wpdb->query("
                SHOW
                    INDEXES
                FROM
                    `" . $tableName . "`
                WHERE
                    `Key_name` = 'uid'
            "
            );

            if (! $checkNewKey) {
                // Add key
                $wpdb->query("
                    ALTER TABLE
                        `" . $tableName . "`
                    ADD KEY
                        `uid` (`uid`, `is_latest`)
                "
                );
            }
        }

        // Change column of cookie_expiry
        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                ALTER TABLE
                    " . $tableNameCookies . "
                MODIFY
                    `cookie_expiry` varchar(255) NOT NULL DEFAULT ''
            "
            );
        }

        update_option('BorlabsCookieVersion', '2.1.13', 'no');
    }

    public function upgradeVersion_2_1_8()
    {
        // Update Multilanguage
        $languageCodes = [];

        // Polylang
        if (defined('POLYLANG_VERSION')) {
            $polylangLanguages = get_terms('language', ['hide_empty' => false]);

            if (! empty($polylangLanguages)) {
                foreach ($polylangLanguages as $languageData) {
                    if (! empty($languageData->slug) && is_string($languageData->slug)) {
                        $languageCodes[$languageData->slug] = $languageData->slug;
                    }
                }
            }
        }

        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            $wpmlLanguages = apply_filters('wpml_active_languages', null, []);

            if (! empty($wpmlLanguages)) {
                foreach ($wpmlLanguages as $languageData) {
                    if (! empty($languageData['code'])) {
                        $languageCodes[$languageData['code']] = $languageData['code'];
                    }
                }
            }
        }

        if (! empty($languageCodes)) {
            foreach ($languageCodes as $languageCode) {
                // Load config
                Config::getInstance()->loadConfig($languageCode);

                // Save CSS
                CSS::getInstance()->save($languageCode);

                // Update style version
                $styleVersion = get_option('BorlabsCookieStyleVersion_' . $languageCode, 1);
                $styleVersion = intval($styleVersion) + 1;

                update_option('BorlabsCookieStyleVersion_' . $languageCode, $styleVersion, false);
            }
        } else {
            // Load config
            Config::getInstance()->loadConfig();

            // Save CSS
            CSS::getInstance()->save();
        }

        update_option('BorlabsCookieVersion', '2.1.8', 'no');
    }

    public function upgradeVersion_2_1_9()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableNameScriptBlocker = $wpdb->prefix
            . 'borlabs_cookie_script_blocker'; // ->prefix contains base_prefix + blog id

        // Check if Script Blocker table is wrong schema
        $columnStatus = Install::getInstance()->checkIfColumnExists($tableNameScriptBlocker,
            'content_blocker_id'
        );

        if ($columnStatus === true) {
            // Fix Script Blocker Table
            $wpdb->query("DROP TABLE IF EXISTS `" . $tableNameScriptBlocker . "`");

            $sqlCreateTableScriptBlocker = Install::getInstance()
                ->getCreateTableStatementScriptBlocker($tableNameScriptBlocker,
                    $charsetCollate
                );

            $wpdb->query($sqlCreateTableScriptBlocker);
        }

        update_option('BorlabsCookieVersion', '2.1.9', 'no');
    }

    public function upgradeVersion_2_2_0()
    {
        global $wpdb;

        // Update tables
        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                ALTER TABLE
                    `" . $tableNameCookies . "`
                MODIFY
                    `cookie_name` TEXT NOT NULL
            "
            );

            $wpdb->query("
                ALTER TABLE
                    `" . $tableNameCookies . "`
                MODIFY
                    `cookie_expiry` TEXT NOT NULL
            "
            );
        }

        update_option('BorlabsCookieVersion', '2.2.0', 'no');
    }

    public function upgradeVersion_2_2_2()
    {
        global $wpdb;

        Log::getInstance()->info(__METHOD__, 'Update Ezoic setup');

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `fallback_js` = '" . esc_sql(Ezoic::getInstance()->getDefault()['fallbackJS']
                ) . "'
                WHERE
                    `service` = 'Ezoic'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}'
                WHERE
                    `service` = 'EzoicMarketing'
                    OR
                    `service` = 'EzoicPreferences'
                    OR
                    `service` = 'EzoicStatistics'
            "
            );
        }

        // Update Multilanguage
        $languageCodes = [];

        // Polylang
        if (defined('POLYLANG_VERSION')) {
            $polylangLanguages = get_terms('language', ['hide_empty' => false]);

            if (! empty($polylangLanguages)) {
                foreach ($polylangLanguages as $languageData) {
                    if (! empty($languageData->slug) && is_string($languageData->slug)) {
                        $languageCodes[$languageData->slug] = $languageData->slug;
                    }
                }
            }
        }

        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            $wpmlLanguages = apply_filters('wpml_active_languages', null, []);

            if (! empty($wpmlLanguages)) {
                foreach ($wpmlLanguages as $languageData) {
                    if (! empty($languageData['code'])) {
                        $languageCodes[$languageData['code']] = $languageData['code'];
                    }
                }
            }
        }

        if (! empty($languageCodes)) {
            foreach ($languageCodes as $languageCode) {
                Log::getInstance()
                    ->info(__METHOD__, 'Update CSS of language {languageCode}', ['languageCode' => $languageCode]);

                // Load config
                Config::getInstance()->loadConfig($languageCode);

                // Save CSS
                CSS::getInstance()->save($languageCode);

                // Update style version
                $styleVersion = get_option('BorlabsCookieStyleVersion_' . $languageCode, 1);
                $styleVersion = intval($styleVersion) + 1;

                update_option('BorlabsCookieStyleVersion_' . $languageCode, $styleVersion, false);
            }
        } else {
            Log::getInstance()->info(__METHOD__, 'Update CSS');

            // Load config
            Config::getInstance()->loadConfig();

            // Save CSS
            CSS::getInstance()->save();
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.2', 'no');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    public function upgradeVersion_2_2_29()
    {
        global $wpdb;

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';
        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `privacy_policy_url` = 'https://wiki.osmfoundation.org/wiki/Privacy_Policy'
                WHERE
                    `privacy_policy_url` = 'https://wiki.osmfoundation.org/wiki/Privacy_Politik'
            "
            );
        }

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_content_blocker';
        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `privacy_policy_url` = 'https://wiki.osmfoundation.org/wiki/Privacy_Policy'
                WHERE
                    `privacy_policy_url` = 'https://wiki.osmfoundation.org/wiki/Privacy_Politik'
            "
            );
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.29', 'no');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    public function upgradeVersion_2_2_3()
    {
        global $wpdb;

        Log::getInstance()->info(__METHOD__, 'Update Ezoic setup');

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_in_js` = '" . esc_sql(Ezoic::getInstance()->getDefault()['optInJS']
                ) . "',
                    `fallback_js` = ''
                WHERE
                    `service` = 'Ezoic'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_out_js` = '" . esc_sql(EzoicMarketing::getInstance()->getDefault()['optOutJS']
                ) . "'
                WHERE
                    `service` = 'EzoicMarketing'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_out_js` = '" . esc_sql(EzoicPreferences::getInstance()->getDefault()['optOutJS']
                ) . "'
                WHERE
                    `service` = 'EzoicPreferences'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_out_js` = '" . esc_sql(EzoicStatistics::getInstance()->getDefault()['optOutJS']
                ) . "'
                WHERE
                    `service` = 'EzoicStatistics'
            "
            );
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.3', 'no');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    public function upgradeVersion_2_2_43()
    {
        global $wpdb;

        // Change povider column length
        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                ALTER TABLE
                    " . $tableNameCookies . "
                MODIFY
                    `provider` varchar(255) NOT NULL DEFAULT ''
            "
            );
        }

        // Update address of cookies
        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';
        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            // Ezoic
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('Ezoic Inc, 6023 Innovation Way 2nd Floor, Carlsbad, CA 92009, USA'
                ) . "'
                WHERE
                    `service` = 'Ezoic'
                    OR
                    `service` = 'EzoicMarketing'
                    OR
                    `service` = 'EzoicPreferences'
                    OR
                    `service` = 'EzoicStatistics'
            "
            );
            // Facebook
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('Meta Platforms Ireland Limited, 4 Grand Canal Square, Dublin 2, Ireland')
                . "'
                WHERE
                    `service` = 'FacebookPixel'
                    OR
                    `cookie_id` = 'facebook'
                    OR
                    `cookie_id` = 'instagram'
            "
            );
            // Google
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Ireland')
                . "'
                WHERE
                    `service` = 'GoogleAds'
                    OR
                    `service` = 'GoogleAdSense'
                    OR
                    `service` = 'GoogleAnalytics'
                    OR
                    `service` = 'GoogleTagManager'
                    OR
                    `service` = 'GoogleTagManagerConsent'
                    OR
                    `cookie_id` = 'googlemaps'
                    OR
                    `cookie_id` = 'youtube'
            "
            );
            // Hotjar
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '"
                . esc_sql('Hotjar Ltd., Dragonara Business Centre, 5th Floor, Dragonara Road, Paceville St Julian\'s STJ 3141 Malta'
                ) . "'
                WHERE
                    `service` = 'Hotjar'
            "
            );
            // HubSpot
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('HubSpot Inc., 25 First Street, 2nd Floor, Cambridge, MA 02141, USA') . "'
                WHERE
                    `service` = 'HubSpot'
            "
            );
            // OpenStreetMap
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '"
                . esc_sql('Openstreetmap Foundation, St John’s Innovation Centre, Cowley Road, Cambridge CB4 0WS, United Kingdom'
                ) . "'
                WHERE
                    `cookie_id` = 'openstreetmap'
            "
            );
            // Tidio
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('Tidio LLC, 220C Blythe Road, London W14 0HH, United Kingdom') . "'
                WHERE
                    `service` = 'Tidio'
            "
            );
            // Twitter
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '"
                . esc_sql('Twitter International Company, One Cumberland Place, Fenian Street, Dublin 2, D02 AX07, Ireland'
                ) . "'
                WHERE
                    `cookie_id` = 'twitter'
            "
            );
            // Userlike
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('Userlike UG, Probsteigasse 44-46, 50670 Köln') . "'
                WHERE
                    `service` = 'Userlike'
            "
            );
            // Vimeo
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `provider` = '" . esc_sql('Vimeo Inc., 555 West 18th Street, New York, New York 10011, USA') . "'
                WHERE
                    `cookie_id` = 'vimeo'
            "
            );
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.43', 'yes');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    public function upgradeVersion_2_2_44()
    {
        $languageCodes = $this->getLanguageCodes();
        if (! empty($languageCodes)) {
            foreach ($languageCodes as $languageCode) {
                Log::getInstance()
                    ->info(__METHOD__, 'Update CSS of language {languageCode}', ['languageCode' => $languageCode]);

                // Load config
                Config::getInstance()->loadConfig($languageCode);
                // Save CSS
                CSS::getInstance()->save($languageCode);
                // Update style version
                $styleVersion = get_option('BorlabsCookieStyleVersion_' . $languageCode, 1);
                $styleVersion = intval($styleVersion) + 1;
                update_option('BorlabsCookieStyleVersion_' . $languageCode, $styleVersion, false);
            }
        } else {
            Log::getInstance()->info(__METHOD__, 'Update CSS');
            // Load config
            Config::getInstance()->loadConfig();
            // Save CSS
            CSS::getInstance()->save();
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.44', 'yes');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    public function upgradeVersion_2_2_45()
    {
        $languageCodes = $this->getLanguageCodes();
        if (! empty($languageCodes)) {
            foreach ($languageCodes as $languageCode) {
                Log::getInstance()
                    ->info(__METHOD__, 'Update CSS of language {languageCode}', ['languageCode' => $languageCode]);

                // Load config
                Config::getInstance()->loadConfig($languageCode);
                // Save CSS
                CSS::getInstance()->save($languageCode);
                // Update style version
                $styleVersion = get_option('BorlabsCookieStyleVersion_' . $languageCode, 1);
                $styleVersion = intval($styleVersion) + 1;
                update_option('BorlabsCookieStyleVersion_' . $languageCode, $styleVersion, false);
            }
        } else {
            Log::getInstance()->info(__METHOD__, 'Update CSS');
            // Load config
            Config::getInstance()->loadConfig();
            // Save CSS
            CSS::getInstance()->save();
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.45', 'yes');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }


    public function upgradeVersion_2_2_6()
    {
        global $wpdb;

        Log::getInstance()->info(__METHOD__, 'Update Ezoic setup');

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_cookies';

        if (Install::getInstance()->checkIfTableExists($tableNameCookies)) {
            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_in_js` = '" . esc_sql(Ezoic::getInstance()->getDefault()['optInJS']
                ) . "',
                    `fallback_js` = ''
                WHERE
                    `service` = 'Ezoic'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_in_js` = '" . esc_sql(EzoicMarketing::getInstance()->getDefault()['optInJS']
                ) . "',
                    `opt_out_js` = '" . esc_sql(EzoicMarketing::getInstance()->getDefault()['optOutJS']
                ) . "'
                WHERE
                    `service` = 'EzoicMarketing'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_in_js` = '" . esc_sql(EzoicPreferences::getInstance()->getDefault()['optInJS']
                ) . "',
                    `opt_out_js` = '" . esc_sql(EzoicPreferences::getInstance()->getDefault()['optOutJS']
                ) . "'
                WHERE
                    `service` = 'EzoicPreferences'
            "
            );

            $wpdb->query("
                UPDATE
                    `" . $tableNameCookies . "`
                SET
                    `settings` = 'a:2:{s:25:\"blockCookiesBeforeConsent\";s:1:\"0\";s:10:\"prioritize\";s:1:\"1\";}',
                    `opt_in_js` = '" . esc_sql(EzoicStatistics::getInstance()->getDefault()['optInJS']
                ) . "',
                    `opt_out_js` = '" . esc_sql(EzoicStatistics::getInstance()->getDefault()['optOutJS']
                ) . "'
                WHERE
                    `service` = 'EzoicStatistics'
            "
            );
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.6', 'no');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    public function upgradeVersion_2_2_9()
    {
        // Update Multilanguage
        $languageCodes = [];

        // Polylang
        if (defined('POLYLANG_VERSION')) {
            $polylangLanguages = get_terms('language', ['hide_empty' => false]);

            if (! empty($polylangLanguages)) {
                foreach ($polylangLanguages as $languageData) {
                    if (! empty($languageData->slug) && is_string($languageData->slug)) {
                        $languageCodes[$languageData->slug] = $languageData->slug;
                    }
                }
            }
        }

        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            $wpmlLanguages = apply_filters('wpml_active_languages', null, []);

            if (! empty($wpmlLanguages)) {
                foreach ($wpmlLanguages as $languageData) {
                    if (! empty($languageData['code'])) {
                        $languageCodes[$languageData['code']] = $languageData['code'];
                    }
                }
            }
        }

        if (! empty($languageCodes)) {
            foreach ($languageCodes as $languageCode) {
                Log::getInstance()
                    ->info(__METHOD__, 'Update CSS of language {languageCode}', ['languageCode' => $languageCode]);

                // Load config
                Config::getInstance()->loadConfig($languageCode);

                // Save CSS
                CSS::getInstance()->save($languageCode);

                // Update style version
                $styleVersion = get_option('BorlabsCookieStyleVersion_' . $languageCode, 1);
                $styleVersion = intval($styleVersion) + 1;

                update_option('BorlabsCookieStyleVersion_' . $languageCode, $styleVersion, false);
            }
        } else {
            Log::getInstance()->info(__METHOD__, 'Update CSS');

            // Load config
            Config::getInstance()->loadConfig();

            // Save CSS
            CSS::getInstance()->save();
        }

        update_option('BorlabsCookieClearCache', true, 'no');
        update_option('BorlabsCookieVersion', '2.2.9', 'no');

        Log::getInstance()->info(__METHOD__, 'Upgrade complete');
    }

    private function getLanguageCodes()
    {
        $languageCodes = [];

        // Polylang
        if (defined('POLYLANG_VERSION')) {
            $polylangLanguages = get_terms('language', ['hide_empty' => false]);

            if (! empty($polylangLanguages)) {
                foreach ($polylangLanguages as $languageData) {
                    if (! empty($languageData->slug) && is_string($languageData->slug)) {
                        $languageCodes[$languageData->slug] = $languageData->slug;
                    }
                }
            }
        }

        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            $wpmlLanguages = apply_filters('wpml_active_languages', null, []);

            if (! empty($wpmlLanguages)) {
                foreach ($wpmlLanguages as $languageData) {
                    if (! empty($languageData['code'])) {
                        $languageCodes[$languageData['code']] = $languageData['code'];
                    }
                }
            }
        }

        // Weglot
        if (function_exists('weglot_get_original_language') && function_exists('weglot_get_destination_languages')) {
            $originalLanguageCode = weglot_get_original_language();
            $languageCodes = array_merge($languageCodes, [
                $originalLanguageCode => $originalLanguageCode,
            ]);
            foreach (weglot_get_destination_languages() as $destination) {
                $languageCodes = array_merge($languageCodes, [
                    $destination['language_to'] => $destination['language_to'],
                ]);
            }
        }

        return $languageCodes;
    }
}
