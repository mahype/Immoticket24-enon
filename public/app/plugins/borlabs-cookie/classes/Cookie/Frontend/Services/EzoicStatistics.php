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

namespace BorlabsCookie\Cookie\Frontend\Services;

class EzoicStatistics
{
    private static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        add_action('borlabsCookie/cookie/edit/template/settings/EzoicStatistics', [$this, 'additionalSettingsTemplate']
        );
    }

    public function __clone()
    {
        trigger_error('Cloning is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserialize is forbidden.', E_USER_ERROR);
    }

    public function additionalSettingsTemplate($data)
    {
        ?>
        <div class="form-group row">
            <div class="col-sm-8 offset-4">
                <div
                    class="alert alert-warning mt-2"><?php
                    $kbLink = _x(
                        'https://borlabs.io/kb/ezoic/',
                        'Backend / Cookie / Ezoic / Alert Message',
                        'borlabs-cookie'
                    );
                    printf(
                        _x(
                            'Your cookie description needs to be updated. Please read <a href="%s" target="_blank" rel="nofollow noopener noreferrer">%s</a>.',
                            'Backend / Cookie / Ezoic / Alert Message',
                            'borlabs-cookie'
                        ),
                        $kbLink,
                        $kbLink
                    ); ?></div>

            </div>
        </div>
        <?php
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
            'cookieId' => 'ezoic-statistics',
            'service' => 'EzoicStatistics',
            'name' => 'Ezoic - Statistics',
            'provider' => 'Ezoic Inc.',
            'purpose' => _x(
                'Helping to understand how visitors interact with websites by collecting and reporting information anonymously.',
                'Frontend / Cookie / Ezoic - Statistics / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://www.ezoic.com/privacy-policy/',
                'Frontend / Cookie / Ezoic - Statistics / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => 'ez*, __qca, _gid, _ga, _gat, AMP_ECID_EZOIC, __utm*, _ga*',
            'cookieExpiry' => _x('1 Year', 'Frontend / Cookie / Ezoic - Statistics / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => $this->optOutJS(),
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
            ],
            'status' => true,
            'undeletetable' => false,
        ];

        return $data;
    }

    /**
     * optInJS function.
     *
     * @access private
     * @return void
     */
    private function optInJS()
    {
        $code = <<<EOT
<script>
if (typeof window.BorlabsEZConsentCategories == 'object') {
    window.BorlabsEZConsentCategories.statistics = true;
}
</script>
EOT;

        return $code;
    }

    /**
     * optOutJS function.
     *
     * @access private
     * @return void
     */
    private function optOutJS()
    {
        $code = <<<EOT
<script>
if (typeof window.BorlabsEZConsentCategories == 'object') {
    window.BorlabsEZConsentCategories.statistics = false;
}
</script>
EOT;

        return $code;
    }
}
