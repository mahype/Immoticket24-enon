<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Controller;

use WPENON\Model\Energieausweis;

class Frontend {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private $model = null;
	private $view = null;

	/**
	 * @var \WPENON\Model\Energieausweis
	 */
	private $energieausweis = null;
	private $schema = null;

	private $enqueue_scripts = false;

	private function __construct() {
		$this->model = \WPENON\Model\EnergieausweisManager::instance();

		add_action( 'wp', array( $this, '_loadEnergieausweis' ) );

		add_action( 'template_redirect', array( $this, '_handleRequest' ) );

		add_action( 'wp_enqueue_scripts', array( $this, '_enqueueScripts' ), 20 );

		add_filter( 'the_content', array( $this, '_createOutput' ) );
	}

	public function _loadEnergieausweis() {
		if ( $this->energieausweis === null ) {
			$this->energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis();
			$this->schema         = $this->energieausweis !== null ? $this->energieausweis->getSchema() : null;
		}
	}

	public function _handleRequest() {
		if ( is_singular() ) {
			if ( is_a( $this->energieausweis, '\WPENON\Model\Energieausweis' ) ) {
				// prevent search engine indexing
				if ( get_option( 'blog_public' ) ) {
					add_action( 'wp_head', 'wp_no_robots' );
				}

				$action = isset( $_GET['action'] ) ? $_GET['action'] : 'overview';

				if ( ! ( is_user_logged_in() && current_user_can( WPENON_CERTIFICATE_CAP ) ) ) {
					$admin_actions = array(
						'duplicate',
						'confirmation-email-send',
						'xml-datenerfassung-send',
						'xml-zusatzdatenerfassung-send',
						'xml-datenerfassung',
						'xml-zusatzdatenerfassung',
						'xml-datenerfassung-response',
						'xml-zusatzdatenerfassung-response',
					);
					if ( in_array( $action, $admin_actions, true ) ) {
						wp_die( __( 'Zugriff verweigert.', 'wpenon' ), 403 );
					}

					$secret = md5( $this->energieausweis->wpenon_email ) . '-' . $this->energieausweis->wpenon_secret;
					if ( ! isset( $_GET['access_token'] ) || $_GET['access_token'] != $secret ) {
						wp_die( __( 'Der Zugriffsschlüssel für diesen Energieausweis wurde entweder nicht angegeben oder ist falsch.', 'wpenon' ), __( 'Zugriff verweigert', 'wpenon' ), 403 );
					}
				}

				switch ( $action ) {
					case 'duplicate':
						$duplicate_id = $this->energieausweis->duplicate();
						wp_redirect( add_query_arg( array(
							'post_type'                    => 'download',
							'frontend_action'              => $action,
							'frontend_action_id'           => $this->energieausweis->ID,
							'frontend_action_status'       => \WPENON\Util\Format::boolean( $duplicate_id ),
							'frontend_action_duplicate_id' => (int) $duplicate_id,
						), admin_url( 'edit.php' ) ) );
						exit;
						break;
					case 'confirmation-email-send':
						$status = \WPENON\Util\Emails::instance()->send_confirmation_email( $this->energieausweis );
						wp_redirect( add_query_arg( array(
							'post_type'              => 'download',
							'frontend_action'        => $action,
							'frontend_action_id'     => $this->energieausweis->ID,
							'frontend_action_status' => \WPENON\Util\Format::boolean( $status ),
						), admin_url( 'edit.php' ) ) );
						exit;
						break;
					case 'xml-datenerfassung-send':
					case 'xml-zusatzdatenerfassung-send':
						if ( $this->energieausweis->isFinalized() && $this->energieausweis->isPaid() ) {
							if ( $action == 'xml-datenerfassung-send' ) {
								$status = ! $this->energieausweis->isRegistered();
							} else {
								$status = ! $this->energieausweis->isDataSent();
							}
							if ( $status ) {
								if ( $action == 'xml-datenerfassung-send' ) {
									$status = \WPENON\Util\DIBT::assignRegistryID( $this->energieausweis );
								} else {
									$status = \WPENON\Util\DIBT::sendData( $this->energieausweis );
								}
								$redirect_status = $status;
								if ( is_wp_error( $redirect_status ) ) {
									$redirect_status = str_replace( ' ', '+', $redirect_status->get_error_message() );
								} else {
									$redirect_status = \WPENON\Util\Format::boolean( $redirect_status );
								}
								wp_redirect( add_query_arg( array(
									'post_type'              => 'download',
									'frontend_action'        => $action,
									'frontend_action_id'     => $this->energieausweis->ID,
									'frontend_action_status' => $redirect_status,
								), admin_url( 'edit.php' ) ) );
								exit;
							} else {
								wp_die( sprintf( __( 'Die XML-Datei für den Energieausweis %s wurde bereits gesendet.', 'wpenon' ), $this->energieausweis->post_title ) );
							}
						} else {
							wp_die( sprintf( __( 'Der Energieausweis %s ist entweder noch nicht vollständig ausgefüllt oder noch nicht bezahlt worden. Daher kann die XML-Datei noch nicht gesendet werden.', 'wpenon' ), $this->energieausweis->post_title ) );
						}
						break;
					case 'xml-datenerfassung':
					case 'xml-zusatzdatenerfassung':
						if ( $this->energieausweis->isFinalized() ) {
							$mode = str_replace( 'xml-', '', $action );
							$this->energieausweis->getXML( $mode, 'I', false );
							exit;
						} else {
							wp_die( sprintf( __( 'Die eingegebenen Daten für den Energieausweis %s sind noch fehlerhaft oder nicht vollständig. Daher kann die XML-Datei noch nicht erzeugt werden.', 'wpenon' ), $this->energieausweis->post_title ) );
						}
						break;
					case 'xml-datenerfassung-response':
					case 'xml-zusatzdatenerfassung-response':
						$data  = false;
						$title = $this->energieausweis->post_title;
						if ( $action == 'xml-datenerfassung-response' ) {
							$data  = $this->energieausweis->register_response;
							$title .= '-Datenerfassung-Antwort';
						} else {
							$data  = $this->energieausweis->data_response;
							$title .= '-Zusatzdatenerfassung-Antwort';
						}
						if ( $data ) {
							header( 'Content-Type: text/xml; charset=utf-8' );
							header( 'Content-Disposition: inline; filename="' . $title . '.xml"' );
							echo '<?xml version="1.0" encoding="UTF-8"?>';
							echo $data;
							exit;
						} else {
							wp_die( sprintf( __( 'Die XML-Datei für den Energieausweis %s wurde noch nicht gesendet, daher ist momentan noch keine Antwort verfügbar.', 'wpenon' ), $this->energieausweis->post_title ) );
						}
						break;
					case 'pdf-view':
						if ( $this->energieausweis->isFinalized() ) {
							$is_pdf_preview = apply_filters( 'wpenon_is_pdf_preview', ! $this->energieausweis->isPaid(), $this->energieausweis );
							$this->energieausweis->getPDF( 'I', $is_pdf_preview );
							exit;
						} else {
							wp_die( sprintf( __( 'Die eingegebenen Daten für den Energieausweis %s sind noch fehlerhaft oder nicht vollständig. Daher kann der Energieausweis noch nicht angezeigt werden.', 'wpenon' ), $this->energieausweis->post_title ) );
						}
						break;
					case 'receipt-view':
						if ( ! empty( $_GET['id'] ) ) {
							$payment = edd_get_payment( (int) $_GET['id'] );
							if ( $payment ) {
								$payment_customer_id = edd_get_payment_customer_id( $payment->ID );
								$customer            = new \EDD_Customer( $this->energieausweis->wpenon_email );
								if ( empty( $customer ) || (int) $customer->id !== (int) $payment_customer_id ) {
									wp_die( __( 'Die angegebene Rechnungs-ID verweist auf keine Ihrer Rechnungen.', 'wpenon' ) );
								}
							} else {
								$payment = null;
								wp_die( __( 'Ungültige Rechnungs-ID.', 'wpenon' ) );
							}
						} else {
							$payment = $this->energieausweis->getPayment();
						}
						if ( $payment !== null ) {
							$hide_common = false;
							if ( filter_input( INPUT_GET, 'hide_common' ) && is_user_logged_in() && current_user_can( WPENON_CERTIFICATE_CAP ) ) {
								$hide_common = true;
							}
							$receipt = new \WPENON\Model\ReceiptPDF( get_the_title( $payment->ID ), false, $hide_common );
							$receipt->create( $payment );
							$receipt->finalize( 'I' );
							exit;
						} else {
							wp_die( sprintf( __( 'Für den Energieausweis %s ist noch keine Bestellung erfolgt, daher ist keine Rechnung verfügbar.', 'wpenon' ), $this->energieausweis->post_title ) );
						}
						break;
					case 'data-pdf-view':
						$this->energieausweis->getDataPDF( 'I' );
						exit;
					case 'data-pdf-view-anonymized':
						$this->energieausweis->getDataAnonymizedPDF( 'I' );
						exit;
					case 'overview':
					case 'edit':
					case 'editoverview':
					case 'purchase':
						$form     = \WPENON\Model\EnergieausweisForm::instance();
						$function = 'handle' . ucfirst( $action ) . 'PageRequest';
						if ( is_callable( array( $form, $function ) ) ) {
							$this->view            = new \WPENON\View\FrontendBase( call_user_func( array(
								$form,
								$function
							), $this->energieausweis ) );
							$this->enqueue_scripts = true;
						}
						break;
					default:
						wp_die( __( 'Ungültiger action-Parameter angegeben.', 'wpenon' ) );
				}
			} else {
				$createpage_slug = $this->model->getCreatePage();
				if ( $createpage_slug ) {
					$form = \WPENON\Model\EnergieausweisForm::instance();

					$this->schema = $form->getCreateSchema( $createpage_slug );
					$this->view   = new \WPENON\View\FrontendBase( $form->handleCreatePageRequest( $createpage_slug ) );

					$this->enqueue_scripts = true;
				}
			}
		}
	}

	public function _createOutput( $output = '' ) {
		if ( $this->view !== null && is_a( $this->view, '\WPENON\View\FrontendBase' ) ) {
			ob_start();
			do_action( 'wpenon_before_content', $this->energieausweis, $this->view );
			$this->view->displayTemplate();
			do_action( 'wpenon_after_content', $this->energieausweis, $this->view );
			$output = ob_get_clean();
		}

		return $output;
	}

	public function _enqueueScripts() {
		if ( $this->enqueue_scripts ) {
			\WPENON\Controller\General::instance()->_enqueueScripts( $this->energieausweis, $this->schema );

			$settings = \WPENON\Util\Settings::instance();

			$css_dependencies = array( 'wpenon-bootstrap' );
			if ( $settings->custom_bootstrap_css != '' ) {
				$css_dependencies = array( $settings->custom_bootstrap_css );
			} else {
				wpenon_enqueue_style( 'wpenon-bootstrap', 'frontend-bootstrap', array() );
			}

			$js_dependencies = array( 'wpenon-bootstrap' );
			if ( $settings->custom_bootstrap_js != '' ) {
				$js_dependencies = array( $settings->custom_bootstrap_js );
			} else {
				wpenon_enqueue_script( 'wpenon-bootstrap', 'third-party/bootstrap/dist/js/bootstrap', array( 'jquery' ), '3.3.2' );
			}

			wpenon_enqueue_style( 'wpenon-frontend', 'frontend', $css_dependencies );
			wpenon_enqueue_script( 'wpenon-frontend', 'frontend', array_merge( $js_dependencies, array(
				'jquery',
				'wpenon-general'
			) ) );
		}
	}

	public function getModel() {
		return $this->model;
	}

	public function getView() {
		return $this->view;
	}
}
