<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                    developed by Borlabs GmbH
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2022 Borlabs GmbH. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs GmbH, https://borlabs.io
 * @author Benjamin A. Bornschein
 *
 */

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Themes;

class Enfold
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * __construct function.
     */
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
     * modifyVideoOutput function.
     *
     * @param mixed $output
     * @param mixed $atts
     * @param mixed $content
     * @param mixed $shortcodename
     * @param mixed $meta
     * @param mixed $video_html_raw
     */
    public function modifyVideoOutput($output, $atts, $content, $shortcodename, $meta, $video_html_raw)
    {
        if (!empty($atts['src'])) {
            $style = [];

            if (!empty($atts['format']) && $atts['format'] == 'custom') {
                $height = (int) ($atts['height']);
                $width = (int) ($atts['width']);
                $ratio = (100 / $width) * $height;
                $style[] = 'padding-bottom:' . $ratio . '%';
            }

            if (!empty($atts['mobile_image'])) {
                $style[] = "background-image: url('" . $atts['mobile_image'] . "')";
            }

            if (!empty($atts['conditional_play']) && $atts['conditional_play'] === 'lightbox') {
                // Nothing for now - can not be supported
            } else {
                $output = '<div class="avia-video avia-video-' . $atts['format'] . '" style="' . implode(';', $style)
                    . '" itemprop="video" itemtype="https://schema.org/VideoObject" data-original_url="' . $atts['src']
                    . '">' . $video_html_raw . '</div>';
            }
        }

        return $output;
    }
}
