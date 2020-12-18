<?php
/**
 * Add CSV sparkasse functionalities.
 *
 * @category Class
 * @package  Enon\Misc
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Sparkasse;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Add_CSV_Export implements Task, Actions {
	/**
	 * Run task.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$user = wp_get_current_user();
		$reseller_id = (int) get_user_meta( $user->ID, 'reseller_id', true );

		// Only this reseller has access.
		if ( 321587 !== $reseller_id ) {
			return;
		}

		$this->add_actions();
	}

	/**
	 * Add Actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'enon_widget_lead_export_after_postcode', [ $this, 'add_preset' ] );
	}

	/**
	 * Add export links.
	 *
	 * @since 1.0.0
	 */
	public function add_preset() {
		$postcodes = array(
			'heidelberg' => [ 69115, 69117, 69118, 69120, 69121, 69123, 69124, 69126 ],
			'neckargemuend' => [ 69151, 69239, 69245, 69250, 69253, 69256, 69257, 69259, 69434, 74909, 74931 ],
			'walldorf_wiesloch' => [ 68789, 69168, 69181, 69190, 69207, 69226, 69231, 69234, 69242, 69254, 74918 ],
			'schwetzingen' => [ 68723, 68775, 68782, 69214 ],
			'hockenheim' => [ 68766, 68799, 68804, 68809 ],
		);

        $postcodes = array_merge( $postcodes['heidelberg'], $postcodes['neckargemuend'], $postcodes['walldorf_wiesloch'], $postcodes['schwetzingen'], $postcodes['hockenheim'] );
        $postcodes = implode( ', ', $postcodes );
        
        ?>  
            <div class="button-group">
                <a id="sparkasse-in-range" class="button">Im Geschäftsbereich</a>
                <a id="sparkasse-out-range" class="button">Außerhalb des Geschäftsbereichs</a>
            </div>

            <script type="text/javascript">
            var sparkasse_postcodes = [ <?php echo $postcodes; ?> ];

            document.getElementById( 'sparkasse-in-range' ).addEventListener('click', function() {
               add_postcodes();
               document.getElementById('filter_postcodes_direction').value = 'include';
            });

            document.getElementById( 'sparkasse-out-range' ).addEventListener('click', function() {
               add_postcodes();
               document.getElementById('filter_postcodes_direction').value = 'exclude';
            });

            function add_postcodes() {
                document.getElementById('filter_postcodes').value = '';

                sparkasse_postcodes.forEach( function (postcode) {
                    document.getElementById('filter_postcodes').value += postcode + '\r\n';
                });
            }
        </script>
        <?php
	}
}
