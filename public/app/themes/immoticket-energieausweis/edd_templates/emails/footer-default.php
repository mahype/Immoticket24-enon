<?php
/**
 * Email Footer
 *
 * @author    Easy Digital Downloads
 * @package   Easy Digital Downloads/Templates/Emails
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
$template_footer = "
  border-top:0;
  -webkit-border-radius:3px;
";

$credit = "
  border:0;
  color: #000000;
  font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
  font-size:12px;
  line-height:125%;
  text-align:center;
";

$business_data = apply_filters( 'wpenon_email_footer_businessdata', immoticketenergieausweis_get_option( 'it-business' ) );
$alternative_footer = apply_filters( 'wpenon_alternative_email_footer', false );
if ( ! empty( $business_data['firmenname'] ) ) {
  $firmenname = $business_data['firmenname'];
} else {
  $firmenname = get_bloginfo( 'name' );
}
?>
                              </div>
                            </td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Footer -->
	                                <?php if( ! $alternative_footer ): ?>
                                    <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="<?php echo $template_footer; ?>">
                                        <tr>
                                            <td valign="top">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="credit" style="<?php echo $credit; ?>">
                                                          <p style="margin-top: 0 !important;">
                                                            <strong><?php echo $firmenname; ?></strong><br />
                                                            <?php if( ! empty($business_data['strassenr']) ): ?><?php echo $business_data['strassenr']; ?><br /><?php endif; ?>
	                                                        <?php if( ! empty($business_data['plz']) ): ?><?php echo $business_data['plz'] . ' ' . $business_data['ort']; ?><br /><?php endif; ?>
	                                                        <?php if( ! empty($business_data['telefon']) ): ?>Telefon: <?php echo $business_data['telefon']; ?><br /><?php endif; ?>
	                                                        <?php if( ! empty($business_data['email']) ): ?> Email: <?php echo str_replace( '@', '(at)', $business_data['email'] ); ?><?php endif; ?>
                                                          </p>

                                                          <p>
	                                                        <?php if( ! empty($business_data['geschaeftsfuehrer']) ): ?>
                                                            Geschäftsführer: <?php
                                                            $first = true;
                                                            foreach ( $business_data['geschaeftsfuehrer'] as $person ) {
                                                              if ( isset( $person['name'] ) ) {
                                                                if ( ! $first ) {
                                                                  echo ', ';
                                                                }
                                                                echo $person['name'];
                                                                $first = false;
                                                              }
                                                            }
                                                            ?><br />
	                                                        <?php endif; ?>
	                                                        <?php if( ! empty($business_data['handelsregister']) ): ?><?php echo $business_data['handelsregister']; ?><br /><?php endif; ?>
	                                                        <?php if( ! empty($business_data['ustidnr']) ): ?>USt-Identifikationsnummer: <?php echo $business_data['ustidnr']; ?><?php endif; ?>
                                                          </p>
                                                          <?php echo wpautop( wp_kses_post( wptexturize( apply_filters( 'edd_email_footer_text', '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>' ) ) ) ); ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
	                                <?php else: ?>
	                                    <?php echo $alternative_footer; ?>
	                                <?php endif; ?>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
