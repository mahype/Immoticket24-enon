<?php

namespace DevOwl\RealCategoryLibrary\lite\view;

\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
trait WooCommerce {
    // Documented in IOverrideWooCommerce
    public function init() {
        if (\class_exists('WooCommerce')) {
            $this->applyHierarchicalAttributes();
        }
    }
    // Documented in IOverrideWooCommerce
    public function applyHierarchicalAttributes() {
        $taxos = wc_get_attribute_taxonomies();
        foreach ($taxos as $tax) {
            $name = wc_attribute_taxonomy_name($tax->attribute_name);
            if ($name) {
                add_filter('woocommerce_taxonomy_args_' . $name, [$this, 'applyHierarchicalAttribute']);
            }
        }
    }
    /**
     * Set the attribute hierarchical.
     *
     * @param array $args
     */
    public function applyHierarchicalAttribute($args) {
        $args['hierarchical'] = \true;
        return $args;
    }
    // Documented in IOverrideWooCommerce
    public function isWooCommerceTaxonomy($taxonomy) {
        $needle = 'pa_';
        return (\class_exists('WooCommerce') && \substr($taxonomy, 0, \strlen($needle)) === $needle) ||
            $taxonomy === 'product_cat';
    }
}
