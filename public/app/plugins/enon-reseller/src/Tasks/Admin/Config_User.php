<?php
/**
 * Configuring user.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Admin;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Config_User
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Config_User implements Actions, Task {

	/**
	 * Running tasks.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Add actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		if ( is_super_admin() ) {
			add_action( 'show_user_profile', array( $this, 'show_reseller_field' ) );
			add_action( 'edit_user_profile', array( $this, 'show_reseller_field' ) );

			add_action( 'personal_options_update', array( $this, 'save_reseller_field' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_reseller_field' ) );
		}
	}

	/**
	 * Add reseller field.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function show_reseller_field( \WP_User $user ) {
		$args = array(
			'post_type'      => 'reseller',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => '',
			'order'          => 'ASC',
		);

		$resellers = get_posts( $args );
		$reseller_id = (int) get_user_meta( $user->ID, 'reseller_id', true );

		?>
		<h3><?php _e( "Reseller settings", "blank" ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="address"><?php _e( "Reseller" ); ?></label></th>
				<td>
					<select name="reseller_id">
						<option value="0"><?php _e('Wählen' ); ?></option>
						<?php foreach( $resellers AS $reseller ): ?>
							<?php $reseller_name = get_post_meta( $reseller->ID, 'company_name', true ); ?>
							<?php if( $reseller_id == $reseller->ID ): ?>
								<option selected="selected" value="<?php echo $reseller->ID;?>"><?php echo $reseller_name; ?></option>
							<?php else: ?>
								<option value="<?php echo $reseller->ID;?>"><?php echo $reseller_name; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<span class="description"><?php _e( "Select a resseler." ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save reseller field.
	 *
	 * @param int $user_id User id.
	 *
	 * @since 1.0.0
	 */
	public function save_reseller_field( $user_id ) {
		update_user_meta( $user_id, 'reseller_id', $_POST['reseller_id'] );
	}
}
