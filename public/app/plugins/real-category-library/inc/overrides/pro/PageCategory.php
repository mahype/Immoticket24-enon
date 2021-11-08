<?php

namespace DevOwl\RealCategoryLibrary\lite;

use DevOwl\RealCategoryLibrary\base\UtilsProvider;
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
/**
 * Adds a taxonomy to the 'page' post type.
 */
class PageCategory {
    use UtilsProvider;
    /**
     * Checks if there is already a taxonomy registered.
     */
    public function check() {
        $taxonomy_objects = get_object_taxonomies('page', 'objects');
        if (\is_array($taxonomy_objects) && \count($taxonomy_objects) > 0) {
            foreach ($taxonomy_objects as $key => $value) {
                if (\boolval($value->hierarchical)) {
                    return \false;
                }
            }
        }
        return \true;
    }
    /**
     * Register a taxonomy to the pages.
     */
    public function register_page_cat() {
        if (!$this->check()) {
            return;
        }
        $labels = [
            'name' => __('Page Categories', RCL_TD),
            'singular_name' => __('Page Category', RCL_TD),
            'menu_name' => __('Categories', RCL_TD)
        ];
        /**
         * This filter allows you to rename the taxonomy name of the page category. Default is `page_cat`.
         * If you want to modify arguments, use https://developer.wordpress.org/reference/hooks/register_taxonomy_args/
         *
         * @param {string} $taxonomyName
         * @return {string}
         * @hook RCB/PageCategory/TaxonomyName
         * @since 4.1.2
         */
        $taxonomyName = apply_filters('RCB/PageCategory/TaxonomyName', 'page_cat');
        $args = [
            'label' => __('Page Categories', RCL_TD),
            'labels' => $labels,
            'public' => \true,
            'hierarchical' => \true,
            'label' => 'Page Categories',
            'show_ui' => \true,
            'query_var' => \true,
            'rewrite' => ['slug' => $taxonomyName, 'with_front' => \true],
            'show_admin_column' => \false
        ];
        register_taxonomy($taxonomyName, ['page'], $args);
    }
}
