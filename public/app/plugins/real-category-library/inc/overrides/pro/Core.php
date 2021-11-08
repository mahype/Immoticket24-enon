<?php

namespace DevOwl\RealCategoryLibrary\lite;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\Freemium\CorePro;
use DevOwl\RealCategoryLibrary\TaxTree;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
trait Core {
    use CorePro;
    /**
     * The updater instance.
     *
     * @see https://github.com/Capevace/wordpress-plugin-updater
     */
    private $updater;
    // Documented in IOverrideCore
    public function overrideConstruct() {
        add_action('init', [new \DevOwl\RealCategoryLibrary\lite\PageCategory(), 'register_page_cat'], 99999);
        add_filter(
            'register_taxonomy_args',
            [\DevOwl\RealCategoryLibrary\TaxTree::class, 'register_taxonomy_args'],
            10,
            3
        );
        add_filter('cptui_attach_post_types_to_taxonomy', [$this, 'cptui_attach_post_types_to_taxonomy']);
    }
    /**
     * Adjust the visible post types for a new taxonomy when it comes from the config page.
     * Usually, CPT UI only shows public post types.
     *
     * @param array $args
     */
    public function cptui_attach_post_types_to_taxonomy($args) {
        unset($args['public']);
        return $args;
    }
    // Documented in IOverrideCore
    public function overrideInit() {
        // Silence is golden.
    }
}
