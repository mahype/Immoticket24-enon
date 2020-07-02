<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                      developed by Borlabs
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2020 Borlabs - Benjamin A. Bornschein. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs - Benjamin A. Bornschein, https://borlabs.io
 * @author Benjamin A. Bornschein, Borlabs ben@borlabs.io
 *
 */

namespace BorlabsCookie\Cookie\Frontend\Services;

class Ezoic
{
    private static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * __construct function.
     *
     * @access protected
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * getDefault function.
     *
     * @access public
     * @return void
     */
    public function getDefault()
    {
        $data = [
            'cookieId' => 'ezoic',
            'service' => 'Ezoic',
            'name' => 'Ezoic',
            'provider' => 'Ezoic Inc.',
            'purpose' => _x('Necessary for the basic functions of the website.', 'Frontend / Cookie / Ezoic / Text', 'borlabs-cookie'),
            'privacyPolicyURL' => _x('https://www.ezoic.com/privacy-policy/', 'Frontend / Cookie / Ezoic / Text', 'borlabs-cookie'),
            'hosts' => [],
            'cookieName' => 'ez*, cf*, unique_id, __cf*, __utmt*',
            'cookieExpiry' => _x('1 Year', 'Frontend / Cookie / Ezoic / Text', 'borlabs-cookie'),
            'optInJS' => '',
            'optOutJS' => '',
            'fallbackJS' => $this->fallbackJS(),
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
            ],
            'status' => true,
            'undeletetable' => false,
        ];

        return $data;
    }

    /**
     * fallbackJS function.
     *
     * @access private
     * @return void
     */
    private function fallbackJS()
    {
        $code = <<<EOT
<script>
document.addEventListener("borlabs-cookie-code-unblocked-after-consent", function (e) {
    window.ezConsentCategories.preferences = window.ezConsentCategories.preferences || false;
    window.ezConsentCategories.statistics = window.ezConsentCategories.statistics || false;
    window.ezConsentCategories.marketing = window.ezConsentCategories.marketing || false;

    if (typeof ezConsentCategories == 'object' && typeof __ezconsent == 'object') {
        __ezconsent.setEzoicConsentSettings(window.ezConsentCategories);
    }
}, false);
</script>
EOT;
        return $code;
    }
}
