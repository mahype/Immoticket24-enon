<?php
/**
 * @version 1.0.2
 *
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Controller;

class Admin
{
    private static $instance;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $model = null;
    private $view = null;

    private $energieausweis = null;

    private $enqueue_scripts = false;

    private function __construct()
    {
        $this->model = \WPENON\Model\EnergieausweisManager::instance();

        add_action('current_screen', array($this, '_handleRequest'), 10, 1);

        add_action('admin_menu', array($this, '_hideAddContentMenu'), 20);
        add_action('admin_enqueue_scripts', array($this, '_enqueueScripts'), 20);

        add_action('edd_bulk_wpenon_receipt_view', array($this, '_processReceiptBulkAction'), 10, 1);
        add_action('edd_wpenon_receipt_view', array($this, '_processReceiptRowAction'), 10, 1);
    }

    public function _handleRequest($screen)
    {
        if ($screen->post_type == 'download') {
            $this->view = new \WPENON\View\AdminBase();

            add_action('admin_notices', array($this, '_checkRegistryIDsLeft'), 20);
            add_action('admin_head', array($this, '_hideAddContent'), 20);

            switch ($screen->base) {
                case 'post':
                    if ($screen->action == 'add') {
                        wp_die(__('Neue Energieausweise können ausschließlich über das Frontend der Website hinzugefügt werden.', 'wpenon'), __('Unerlaubter Zugriff', 'wpenon'));
                    }

                    if (isset($_GET['post'])) {
                        $energieausweis = \WPENON\Model\EnergieausweisManager::instance()->getEnergieausweis((int) $_GET['post']);
                        if ($energieausweis) {
                            do_action('wpenon_admin_edit_certificate', $energieausweis);
                        }
                    }

                    add_action('add_meta_boxes_download', array($this, '_addMetaBoxes'), 10, 1);
                    add_action('save_post_download', array($this, '_saveMetaBoxes'), 10, 3);
                    add_action('edit_form_top', array($this, '_displayNotices'), 10, 1);
                    add_action('edit_form_top', array($this, '_includeNonce'), 10, 1);
                    add_action('post_edit_form_tag', array($this, '_printNovalidate'), 10, 1);
                    add_filter('publish_download', array($this, '_assignTitle'), 10, 2);
                    add_filter('enter_title_here', array($this, '_adjustTitlePlaceholder'), 10, 2);
                    add_filter('get_sample_permalink_html', array($this, '_getSingleActions'), 10, 4);
                    $this->enqueue_scripts = true;
                    break;
                case 'edit':
                    add_filter('edd_download_columns', array($this, '_getColumns'), 10, 1);
                    add_filter('manage_edit-download_sortable_columns', array($this, '_getSortableColumns'), 10, 1);
                    add_action('manage_posts_custom_column', array($this, '_renderColumn'), 10, 2);
                    add_filter('post_row_actions', array($this, '_getRowActions'), 10, 2);
                    add_filter('request', array($this, '_adjustQueryVars'), 10, 1);
                    add_action('admin_notices', array($this, '_showFrontendActionMessages'));
                    break;
                case 'download_page_edd-payment-history':
                    if (isset($_GET['action'])) {
                        $ids = isset($_GET['payment']) ? $_GET['payment'] : array();
                        if (!is_array($ids)) {
                            $ids = array($ids);
                        }

                        if (count($ids) > 0) {
                            switch ($_GET['action']) {
                                case 'mark-deposit-refunded':
                                    foreach ($ids as $id) {
                                        update_post_meta($id, '_wpenon_deposit_refunded', '1');
                                    }
                                    break;
                                case 'unmark-deposit-refunded':
                                    foreach ($ids as $id) {
                                        delete_post_meta($id, '_wpenon_deposit_refunded');
                                    }
                                    break;
                                case 'wpenon-receipt-view':
                                    do_action('edd_bulk_wpenon_receipt_view', $ids);
                                    break;
                            }
                        }
                    }
                    add_filter('edd_payments_table_columns', array($this, '_getPaymentsColumns'));
                    add_filter('edd_payments_table_sortable_columns', array($this, '_getPaymentsSortableColumns'));
                    add_filter('edd_payments_table_column', array($this, '_getPaymentsColumn'), 10, 3);
                    add_filter('edd_payments_table_bulk_actions', array($this, '_getPaymentsBulkActions'));
                    add_filter('edd_payment_row_actions', array($this, '_getPaymentsRowActions'), 10, 2);
                    break;
                case 'download_page_edd-customers':
                    add_filter('edd_report_customer_columns', array($this, '_getCustomersColumns'));
                    add_filter('edd_report_column_address', array($this, '_renderCustomerColumnAddress'), 10, 2);
                    add_filter('edd_customers_column_address', array($this, '_renderCustomerColumnAddress'), 10, 2);
                    add_filter('edd_customers_column_phone', array($this, '_renderCustomerColumnPhone'), 10, 2);
                    break;
                case 'download_page_edd-discounts':
                    remove_action('admin_head', array($this, '_hideAddContent'), 20);
                    break;
                case 'download_page_edd-reports':
                    break;
                default:
            }
        } elseif (strpos($screen->base, 'tabellen_page_'.WPENON_PREFIX) === 0) {
            $table_slug = str_replace('tabellen_page_'.WPENON_PREFIX, '', $screen->base);
            add_action('admin_enqueue_scripts', array($this, '_enqueueAdminStyle'), 20);
        }
    }

    public function _addMetaBoxes($post = null)
    {
        $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis($post);

        if ($this->energieausweis !== null) {
            $this->view->addMetaBoxes($this->energieausweis);
        }
    }

    public function _saveMetaBoxes($post_id, $post = null, $update = false)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['wpenon_edit_energieausweis_nonce']) || !wp_verify_nonce($_POST['wpenon_edit_energieausweis_nonce'], 'wpenon_edit_energieausweis_nonce')) {
            return;
        }

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if ($post->post_status === 'auto-draft') {
            return;
        }

        $post_type_object = get_post_type_object('download');
        if (!current_user_can($post_type_object->cap->edit_post, $post_id)) {
            return;
        }

        if (isset($_POST) && is_array($_POST)) {
            $private_fields = \WPENON\Model\Schema::parseFields(\WPENON\Model\EnergieausweisManager::getPrivateFields());
            foreach ($private_fields as $field_slug => $field) {
                if (isset($_POST[$field_slug])) {
                    if ($field['required'] && $field['display']) {
                        $validated = \WPENON\Util\Validate::notempty($_POST[$field_slug], $field);
                    }

                    if (!isset($validated['error'])) {
                        $validated = \WPENON\Util\Validate::callback($_POST[$field_slug], $field);
                    }

                    if (!isset($validated['error'])) {
                        update_post_meta($post_id, $field_slug, $validated['value']);
                    }
                }
            }
        }

        $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis($post);

        if ($this->energieausweis !== null) {
            $this->view->saveMetaBoxes($this->energieausweis);

            if (!$update) {
                do_action('wpenon_energieausweis_create', $this->energieausweis);
            }
        }
    }

    public function _displayNotices($post = null)
    {
        $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis($post);

        if ($this->energieausweis !== null) {
            $this->view->displayNotices($this->energieausweis);
        }
    }

    public function _includeNonce($post = null)
    {
        wp_nonce_field('wpenon_edit_energieausweis_nonce', 'wpenon_edit_energieausweis_nonce', false);
    }

    public function _assignTitle($post_id, $post = null)
    {
        global $wpdb;

        if (!$post->post_title) {
            $data = array('post_title' => '', 'post_name' => '');
            $data['post_title'] = \WPENON\Model\EnergieausweisManager::_generateTitle($post_id, true);
            $data['post_name'] = sanitize_title($data['post_title']);

            $wpdb->update($wpdb->posts, $data, array('ID' => $post_id));
        }
    }

    public function _adjustTitlePlaceholder($placeholder, $post = null)
    {
        if (!$post->post_title) {
            $title = \WPENON\Model\EnergieausweisManager::_generateTitle();
            $title .= ' '.__('(vorläufiger Titel)', 'wpenon');

            return $title;
        }

        return $placeholder;
    }

    public function _getSingleActions($ret, $post_id, $new_title = '', $new_slug = '')
    {
        $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis($post_id);
        if ($this->energieausweis !== null) {
            $actions = $this->view->getActions(array(), $this->energieausweis);
            foreach ($actions as $slug => $action) {
                $ret .= '<span id="'.$slug.'-btn">'.str_replace(' href="', ' class="button button-small" href="', $action).'</span>';
            }
        }

        return $ret;
    }

    public function _printNovalidate($post = null)
    {
        echo ' novalidate';
    }

    public function _getColumns($columns = array())
    {
        return $this->view->getColumns($columns);
    }

    public function _getSortableColumns($columns = array())
    {
        return $this->view->getSortableColumns($columns);
    }

    public function _renderColumn($column_name, $post_id)
    {
        $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis($post_id);

        if ($this->energieausweis !== null) {
            $this->view->renderColumn($column_name, $this->energieausweis);
        }
    }

    public function _getRowActions($actions, $post = null)
    {
        $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis($post);

        if ($this->energieausweis !== null) {
            return $this->view->getActions($actions, $this->energieausweis);
        }

        return $actions;
    }

    public function _adjustQueryVars($vars = array())
    {
        if (isset($vars['orderby'])) {
            $order = isset($vars['order']) ? $vars['order'] : 'asc';
            $custom_vars = $this->view->getSortQueryVars($vars['orderby'], $order);
            $vars = array_merge_recursive($vars, $custom_vars);
        }
        if (isset($vars['s'])) {
            $custom_vars = $this->view->getSearchQueryVars($vars['s']);
            $vars = array_merge_recursive($vars, $custom_vars);
        }

        return $vars;
    }

    public function _enqueueScripts()
    {
        if ($this->enqueue_scripts) {
            if ($this->energieausweis === null) {
                $this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis();
            }
            $schema = $this->energieausweis !== null ? $this->energieausweis->getSchema() : null;
            \WPENON\Controller\General::instance()->_enqueueScripts($this->energieausweis, $schema, true);

            wpenon_enqueue_style('wpenon-admin', 'admin', array());
            wpenon_enqueue_script('wpenon-admin', 'admin', array('jquery', 'wpenon-general'));
        }
    }

    public function _enqueueAdminStyle()
    {
        wpenon_enqueue_style('wpenon-admin', 'admin', array());
    }

    public function _getPaymentsColumns($columns)
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'title' => __('Rechnungsnummer', 'wpenon'),
            'email' => __('Kunde', 'wpenon'),
            'details' => __('Details', 'easy-digital-downloads'),
            'amount' => __('Amount', 'easy-digital-downloads'),
            'date' => __('Date', 'easy-digital-downloads'),
            'status' => __('Status', 'easy-digital-downloads'),
        );

        return $columns;
    }

    public function _getPaymentsSortableColumns($columns)
    {
        $columns['title'] = array('post_title', true);

        return $columns;
    }

    public function _getPaymentsColumn($value, $post_id, $column_name)
    {
        if (get_post_type($post_id) == 'edd_payment') {
            switch ($column_name) {
                case 'title':
                    $value = get_the_title($post_id);
                    break;
                case 'email':
                    $user_info = edd_get_payment_meta_user_info($post_id);
                    if (strpos($value, $user_info['email']) === 0) {
                        $value = str_replace($user_info['email'], '<a href="'.esc_url(add_query_arg(array(
                                'user' => urlencode($user_info['email']),
                                'paged' => false,
                            ))).'">'.esc_html($user_info['first_name'].' '.$user_info['last_name']).'</a>', $value);
                    }
                    break;
                case 'status':
                    if (get_post_meta($post_id, '_wpenon_deposit_refunded', true)) {
                        if (!empty($value)) {
                            $value .= '<br />';
                        }
                        $value .= __('(Lastschrift zurückgegangen)', 'wpenon');
                    }
                    break;
                default:
                    break;
            }
        }

        return $value;
    }

    public function _getPaymentsBulkActions($bulk_actions)
    {
        $bulk_actions['mark-deposit-refunded'] = __('&#8220;Lastschrift zurückgegangen&#8221;-Markierung hinzufügen', 'wpenon');
        $bulk_actions['unmark-deposit-refunded'] = __('&#8220;Lastschrift zurückgegangen&#8221;-Markierung entfernen', 'wpenon');
        $bulk_actions['resend-receipt'] = __('Zahlungsbestätigungen erneut senden', 'wpenon');
        $bulk_actions['wpenon-receipt-view'] = __('Rechnungen ansehen', 'wpenon');

        return $bulk_actions;
    }

    public function _getPaymentsRowActions($row_actions, $payment)
    {
        $row_actions['wpenon_receipt_view'] = '<a href="'.add_query_arg(array(
                'edd-action' => 'wpenon_receipt_view',
                'purchase_id' => $payment->ID,
            ), admin_url('edit.php?post_type=download&page=edd-payment-history')).'">'.__('Rechnung ansehen', 'wpenon').'</a>';

        return $row_actions;
    }

    public function _getCustomersColumns($columns)
    {
        $columns = array(
            'name' => __('Name', 'easy-digital-downloads'),
            'email' => __('Email', 'easy-digital-downloads'),
            'address' => __('Adresse', 'wpenon'),
            'phone' => __('Telefonnummer', 'wpenon'),
            'num_purchases' => __('Purchases', 'easy-digital-downloads'),
            'amount_spent' => __('Total Spent', 'easy-digital-downloads'),
            'date_created' => __('Date Created', 'easy-digital-downloads'),
        );

        return $columns;
    }

    public function _renderCustomerColumnAddress($value, $customer_id)
    {
        $customer_meta = \WPENON\Util\CustomerMeta::instance()->getCustomerMeta($customer_id);

        $value = $customer_meta['line1'];
        if (!empty($customer_meta['line2'])) {
            $value .= ' '.$customer_meta['line2'];
        }
        $value .= ', '.$customer_meta['zip'].' '.$customer_meta['city'];

        return $value;
    }

    public function _renderCustomerColumnPhone($value, $customer_id)
    {
        $customer_meta = \WPENON\Util\CustomerMeta::instance()->getCustomerMeta($customer_id);

        if (!isset($customer_meta['telefon'])) {
            return '';
        }

        return $customer_meta['telefon'];
    }

    public function _processReceiptBulkAction($payment_ids)
    {
        $receipt = new \WPENON\Model\ReceiptPDF(__('Energieausweis-Rechnungen', 'wpenon'), true);
        foreach ($payment_ids as $payment_id) {
            $payment = $this->get_payment_details($payment_id);
            $receipt->create($payment);
        }
        $receipt->finalize('I');
        exit;
    }

    public function _processReceiptRowAction($args)
    {
        if (isset($args['purchase_id'])) {
            $payment = $this->get_payment_details($args['purchase_id']);

            $receipt = new \WPENON\Model\ReceiptPDF(get_the_title($payment->ID));
            $receipt->create($payment);
            $receipt->finalize('I');
            exit;
        }
    }

    //TODO: maybe EDD will create a separate function for that some time
    public function get_payment_details($payment_id)
    {
        $post = get_post($payment_id);

        $details = new \stdClass();

        $payment_id = $post->ID;

        $details->ID = $payment_id;
        $details->date = $post->post_date;
        $details->post_status = $post->post_status;
        $details->total = edd_get_payment_amount($payment_id);
        $details->subtotal = edd_get_payment_subtotal($payment_id);
        $details->tax = edd_get_payment_tax($payment_id);
        $details->fees = edd_get_payment_fees($payment_id);
        $details->key = edd_get_payment_key($payment_id);
        $details->gateway = edd_get_payment_gateway($payment_id);
        $details->user_info = edd_get_payment_meta_user_info($payment_id);
        $details->cart_details = edd_get_payment_meta_cart_details($payment_id, true);

        if (edd_get_option('enable_sequential')) {
            $details->payment_number = edd_get_payment_number($payment_id);
        }

        return apply_filters('edd_payment', $details, $payment_id, null);
    }

    public function _showFrontendActionMessages()
    {
        if (isset($_GET['frontend_action']) && isset($_GET['frontend_action_id']) && isset($_GET['frontend_action_status'])) {
            $action = $_GET['frontend_action'];
            $id = absint($_GET['frontend_action_id']);
            if ('true' !== $_GET['frontend_action_status'] && 'false' !== $_GET['frontend_action_status']) {
                $status = wp_unslash($_GET['frontend_action_status']);
            } else {
                $status = \WPENON\Util\Parse::boolean($_GET['frontend_action_status']);
            }

            $message = '';
            switch ($action) {
                case 'duplicate':
                    if (is_bool($status) && $status && !empty($_GET['frontend_action_duplicate_id'])) {
                        $duplicate_id = (int) $_GET['frontend_action_duplicate_id'];
                        unset($_GET['frontend_action_duplicate_id']);
                        $_SERVER['REQUEST_URI'] = remove_query_arg(array('frontend_action_duplicate_id'), $_SERVER['REQUEST_URI']);
                        $message = __('Der Ausweis %s wurde erfolgreich dupliziert.', 'wpenon').' <a href="'.esc_url(get_permalink($duplicate_id)).'">'.__('Neuen Ausweis ansehen', 'wpenon').'</a>';
                    } else {
                        $message = __('Der Ausweis %s konnte nicht dupliziert werden.', 'wpenon');
                    }
                    break;
                case 'confirmation-email-send':
                    if (is_bool($status) && $status) {
                        $message = __('Die Bestätigungs-Email für den Ausweis %s wurde erfolgreich gesendet.', 'wpenon');
                    } else {
                        $message = __('Die Bestätigungs-Email für den Ausweis %s konnte nicht gesendet werden.', 'wpenon');
                    }
                    break;
                case 'xml-datenerfassung-send':
                    if (is_bool($status) && $status) {
                        $message = __('Der Ausweis %s wurde erfolgreich beim DIBT registriert.', 'wpenon');
                    } else {
                        $message = __('Der Ausweis %s konnte nicht beim DIBT registriert werden.', 'wpenon');
                    }
                    break;
                case 'xml-zusatzdatenerfassung-send':
                    if (is_bool($status) && $status) {
                        $message = __('Die Daten für den Ausweis %s wurden erfolgreich an das DIBT gesendet.', 'wpenon');
                    } else {
                        $message = __('Die Daten für den Ausweis %s konnten nicht an das DIBT gesendet werden.', 'wpenon');
                    }
                    break;
                default:
            }

            if (is_string($status)) {
                $message = substr($message, 0, -1).': '.$status;
            }

            if (!empty($message)) {
                echo '<div class="notice notice-'.($status ? 'success' : 'error').'">';
                echo '<p>'.sprintf($message, get_the_title($id)).'</p>';
                echo '</div>';
            }

            unset($_GET['frontend_action']);
            unset($_GET['frontend_action_id']);
            unset($_GET['frontend_action_status']);

            $_SERVER['REQUEST_URI'] = remove_query_arg(array(
                'frontend_action',
                'frontend_action_id',
                'frontend_action_status',
            ), $_SERVER['REQUEST_URI']);
        }
    }

    public function _checkRegistryIDsLeft()
    {
        $rest = \WPENON\Util\DIBT::getRegistryIDsLeft();

        if ($rest <= 20) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>'.__('Warnung:', 'wpenon').'</strong> '.sprintf(__('Es sind nur noch %d Registriernummern verfügbar. Bitte erweitern Sie in Kürze das Kontingent beim %s.', 'wpenon'), $rest, '<a href="'.\WPENON\Util\DIBT::getLoginURL().'" target="_blank">'.__('DIBT', 'wpenon').'</a>').'</p>';
            echo '</div>';
        } elseif ($rest <= 5) {
            echo '<div class="notice notice-error">';
            echo '<p><strong>'.__('Achtung:', 'wpenon').'</strong> ';
            if ($rest == 0) {
                echo sprintf(__('Es sind keine Registriernummern verfügbar. Aktuell können neu gekauften Energieausweisen der Kunden deshalb keine Registriernummern zugewiesen werden. Bitte erwerben Sie beim %s umgehend neue Registriernummern.', 'wpenon'), '<a href="'.\WPENON\Util\DIBT::getLoginURL().'" target="_blank">'.__('DIBT', 'wpenon').'</a>');
            } else {
                echo sprintf(__('Es sind nur noch %d Registriernummern verfügbar. Bitte erwerben Sie umgehend beim %s neue Registriernummern, damit diese weiterhin ordnungsgemäß zugewiesen werden können.', 'wpenon'), $rest, '<a href="'.\WPENON\Util\DIBT::getLoginURL().'" target="_blank">'.__('DIBT', 'wpenon').'</a>');
            }
            echo '</p>';
            echo '</div>';
        }
    }

    public function _hideAddContentMenu()
    {
        global $submenu;

        $to_remove = array(10, 15, 16);

        foreach ($to_remove as $menu_position) {
            if (isset($submenu['edit.php?post_type=download'][$menu_position])) {
                unset($submenu['edit.php?post_type=download'][$menu_position]);
            }
        }
    }

    public function _hideAddContent()
    {
        ?>
		<style type="text/css">
			#favorite-actions,
			.page-title-action,
			.add-new-h2 {
				display: none !important;
			}
		</style>
		<?php
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getView()
    {
        return $this->view;
    }
}
