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
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
