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

namespace BorlabsCookie\Cookie\Frontend;

class Buffer
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $buffer = '';

    private $bufferActive = false;

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
     * getBuffer function.
     */
    public function &getBuffer()
    {
        $this->buffer = ob_get_contents();

        return $this->buffer;
    }

    /**
     * endBuffering function.
     */
    public function endBuffering()
    {
        ob_end_clean();

        echo $this->buffer;

        unset($this->buffer);

        $this->bufferActive = false;
    }

    /**
     * handleBuffering function.
     */
    public function handleBuffering()
    {
        $this->startBuffering();
    }

    /**
     * isBufferActive function.
     */
    public function isBufferActive()
    {
        return $this->bufferActive;
    }

    /**
     * startBuffering function.
     */
    public function startBuffering()
    {
        if (ScriptBlocker::getInstance()->isScanActive() || ScriptBlocker::getInstance()->hasScriptBlocker()) {
            ob_start();
            // Allow to disable the buffering when a Page Builder is active
            $this->bufferActive = apply_filters('borlabsCookie/buffer/active', true);
        }
    }
}
