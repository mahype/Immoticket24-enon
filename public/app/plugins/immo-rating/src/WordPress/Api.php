<?php 

namespace AWSM\ImmoRating\WordPress;

use AWSM\LibWP\Component\Component;

class Api extends Component {
    public function addShortcode() {
        add_shortcode( 'immorating', [ $this, 'shortcode' ] );
    }

    public function shortcode() {
        return '<div id="immorating" data-nonce="' . wp_create_nonce( 'wp_rest' ) . '"></div>';
    }

    public static function registerRestRoute()
    {
        register_rest_route( 'awsm/v1', '/submission/', array(
            'methods' => 'POST',
            'callback' => [ __CLASS__, 'postSubmission' ],
            'permission_callback' => '__return_true'
        ) );
    } 

    public static function postSubmission( \WP_REST_Request $request ) {
        $content = '';

        $fieldsets = $request['data'];

        foreach( $fieldsets as $fieldset ) {
            $content.= '<h2>' . $fieldset['label'] . '</h2>';

            foreach( $fieldset['fields'] AS $field ) {
                $content.= '<div style="display:flex;">';
                if ( $field ['label'] ) {
                    $content.= '<div style="width:30%">' . $field['label'] . ': </div>';
                }
                $content.= '<div>' . $field['value'] . '</div>';
                $content.= '</div>';
            }
        }

        add_filter( 'wp_mail_content_type',[ __CLASS__, 'setContentType']  );
        // wp_mail( 'christian@immoticket24.de', 'Immobilienwertermittlung Anfrage', $content );
        wp_mail( 'sven@awesome.ug', 'Immobilienwertermittlung Anfrage', $content );
        remove_filter( 'wp_mail_content_type',[ __CLASS__, 'setContentType']  );    
    }

    public static function setContentType() {
        return 'text/html';
    }
}