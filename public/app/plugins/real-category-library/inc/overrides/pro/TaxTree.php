<?php

namespace DevOwl\RealCategoryLibrary\lite;

\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
trait TaxTree {
    public static $SKIP_AUTOMATIC_HIERARCHICAL_TAXONOMY = [
        'post_tag',
        'post_format',
        'product_type',
        'product_visibility',
        'product_shipping_class',
        // [Plugin comp] Beaver Builder
        'fl-builder-template-type',
        // [Plugin Comp] Divi
        'layout_type'
    ];
    public static $SKIP_AUTOMATIC_HIERARCHICAL_TAXONOMY_BY_PT = [];
    public static $SKIP_AUTOMATIC_HIERARCHICAL_TAXONOMY_CONTAINS = ['_tag', 'tag_', 'language', 'translation'];
    // Documented in IOverrideTaxTree
    public function buildQueryUrl($slug) {
        $query = ['post_type' => $this->getTypeNow()];
        // Check if woocommerce product attribute
        if ($this->isWcTaxonomy || empty($this->taxnow->query_var)) {
            $query['taxonomy'] = $this->getQueryVar();
            $query['term'] = $slug;
        } else {
            $query[$this->getQueryVar()] = $slug;
        }
        return $query;
    }
    // Documented in IOverrideTaxTree
    public function isActive($category) {
        $query = $this->getQueryArgs();
        $slug = $category->editableSlug;
        $catId = $category->term_id;
        if (isset($query[$this->getQueryVar()]) && $query[$this->getQueryVar()] === $slug) {
            return \true;
        } elseif (
            isset($query['taxonomy']) &&
            isset($query['term']) &&
            $query['taxonomy'] === $this->getQueryVar() &&
            $query['term'] === $slug
        ) {
            return \true;
        } elseif (isset($query['cat']) && \intval($query['cat']) === $catId && !isset($query[$this->getQueryVar()])) {
            return \true;
        } else {
            return \false;
        }
    }
    // Documented in IOverrideTaxTree
    public function isCurrentAllPosts() {
        $query = $this->getQueryArgs();
        $catId = isset($query['cat']) ? $query['cat'] : null;
        if (
            (isset($query['taxonomy']) &&
                $this->getCore()
                    ->getWooCommerce()
                    ->isWooCommerceTaxonomy($query['taxonomy'])) ||
            \is_numeric($catId)
        ) {
            // Check if woocommerce product attribute
            return \false;
        } else {
            return empty(isset($query[$this->getQueryVar()]) ? $query[$this->getQueryVar()] : '');
        }
    }
    /**
     * Make all registered taxonomies hierarchical.
     *
     * @param array $args
     * @param string $name
     * @param string[] $post_types
     */
    public static function register_taxonomy_args($args, $name, $post_types) {
        $skip = \in_array($name, self::$SKIP_AUTOMATIC_HIERARCHICAL_TAXONOMY, \true);
        foreach (self::$SKIP_AUTOMATIC_HIERARCHICAL_TAXONOMY_CONTAINS as $taxonomyContains) {
            if ($skip) {
                break;
            }
            $skip = \strpos($name, $taxonomyContains) !== \false;
        }
        // Skip by post type taxonomy
        if (!$skip) {
            foreach ($post_types as $post_type) {
                $skipByPtTaxonomies = self::$SKIP_AUTOMATIC_HIERARCHICAL_TAXONOMY_BY_PT[$post_type] ?? [];
                if (\in_array($name, $skipByPtTaxonomies, \true)) {
                    $skip = \true;
                    break;
                }
            }
        }
        /**
         * The plugin automatically makes all newly registered taxonomies `hierarchical=true`. If a taxonomy
         * strictly needs to be a tag system, you need to explicitly skip the automatic rewrite.
         *
         * @param {boolean} $skip
         * @param {array} $args
         * @param {string} $taxonomy
         * @return {boolean}
         * @hook RCL/Taxonomy/MakeHierarchical
         * @since 3.6.0
         */
        $skip = apply_filters('RCL/Taxonomy/MakeHierarchical', $skip, $args, $name);
        if (!$skip) {
            $args['hierarchical'] = \true;
        }
        return $args;
    }
}
