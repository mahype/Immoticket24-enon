<?php

/**
 * Setting up general WP options
 *
 * @category Class
 * @package  Enon\Config\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Setup_Gutenberg.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 2022-06-13
 */
class Setup_Passwords implements Actions, Task
{
    /**
     * Running tasks.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run()
    {
        $this->add_actions();
    }

    /**
     * Adding Filters.
     *
     * @since 2022-06-13
     */
    public function add_actions()
    {
        add_action('validate_password_reset', [$this, 'check_password_strength']);
        add_action('user_profile_update_errors', [$this, 'check_password_strength']);
    }

    /**
     * Check password strength
     *
     * @since 2022-06-13
     */
    public function check_password_strength($errors)
    {
        $password1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';

        if (empty($password1)) {
            return;
        }

        $error = false;

        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%ß&\^\(\)\*\.,]{8,32}$/', $password1)) {
            $error = true;
        }

        //!@#$%ß()*.,

        if ($error) {
            $errors->add(
                'weak-password',
                __('Das Passwort ist nicht sicher. Das Passwort muss Buchstaben, Zahlen und mindestens ein Sonderzeichen enthalten und aus 8 bis 32 Zeichen bestehen.', 'wpenon'),
                array('form-field' => 'pass1')
            );
        }
    }
}
