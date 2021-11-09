<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient;

use WP_User;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Utils functionality.
 */
class Utils {
    /**
     * Get the full name for the currently logged in user.
     *
     * @param WP_User $user Fall back to current user
     */
    public static function getUserFullName($user = null) {
        $user = $user === null ? wp_get_current_user() : $user;
        if ($user instanceof \WP_User) {
            $names = [];
            if (!empty($user->first_name)) {
                $names[] = $user->first_name;
            }
            if (!empty($user->last_name)) {
                $names[] = $user->last_name;
            }
            return \count($names) > 0 ? \join(' ', $names) : '';
        }
        return '';
    }
    /**
     * Check if the current installation is multisite.
     */
    public static function isMu() {
        return \function_exists('switch_to_blog') && \function_exists('get_network') && get_network() !== null;
    }
    /**
     * Get current home url, normalized without schema and `www` subdomain.
     * This avoids general conflicts for situations, when customers move their
     * HTTP site to HTTPS.
     *
     * @return string Can be empty, e.g. for WP CLI and WP Cronjob when Object Cache is active
     */
    public static function getCurrentHostname() {
        // Check if constant is defined (https://wordpress.org/support/article/changing-the-site-url/#edit-wp-config-php)
        if (\defined('WP_SITEURL')) {
            $site_url = \constant('WP_SITEURL');
        } else {
            // Force so the options cache is filled
            get_option('siteurl');
            // Directly read from our cache cause we want to skip `site_url` / `option_site_url` filters (https://git.io/JOnGV)
            // Why `alloptions`? Due to the fact that `siteurl` is `autoloaded=yes`, it is loaded via `wp_load_alloptions` and filled
            // to the cache key `alloptions`. The filters are used by WPML and PolyLang but we do not care about them
            $alloptions = wp_cache_get('alloptions', 'options');
            $site_url = \is_array($alloptions) ? $alloptions['siteurl'] : site_url();
        }
        $url = \trim(untrailingslashit($site_url), '/');
        $url = \preg_replace('/^http(s)?:\\/\\//', '', $url);
        $url = \preg_replace('/^www\\./', '', $url);
        // Remove default ports (https://regex101.com/r/eyxvPE/1)
        $url = \preg_replace('/:(80|443)$/', '', $url);
        return $url;
    }
    /**
     * To avoid issues with multisites without own domains, we need to map blog ids
     * to their `site_url`'s host so we can determine the used license for a given blog.
     *
     * @param int[] $blogIds
     */
    public static function mapBlogsToHosts($blogIds) {
        // Map blog ids to potential hostnames and reverse
        $hostnames = [];
        $isMu = \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils::isMu();
        foreach ($blogIds as $blogId) {
            if ($isMu) {
                switch_to_blog($blogId);
            }
            $host = \parse_url(site_url(), \PHP_URL_HOST);
            /**
             * Connect a sub site within your multisite with the main host. This is useful e.g. you are
             * running a multisite site per language: de.example.com, en.example.com, ... . This leads to
             * one license bundled for multiple sites.
             *
             * @hook DevOwl/RealProductManager/HostMap/ConnectThematic
             * @param {string} $host
             * @param {int} $blogId
             * @since 1.7.9
             * @return {string}
             */
            $host = apply_filters('DevOwl/RealProductManager/HostMap/ConnectThematic', $host, $blogId);
            $hostnames['blog'][$blogId] = $host;
            $hostnames['host'][$host][] = $blogId;
            if ($isMu) {
                restore_current_blog();
            }
        }
        return $hostnames;
    }
}
