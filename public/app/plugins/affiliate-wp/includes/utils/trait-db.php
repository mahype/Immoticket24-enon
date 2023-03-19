<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- The name of the tile is common among others.
/**
 * Database Utilities
 *
 * @package     AffiliateWP
 * @subpackage  Database
 * @copyright   Copyright (c) 2020, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.12.0
 * @author      Aubrey Portwood <aubrey@awesomeomotive.com>
 */

namespace AffiliateWP\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( trait_exists( '\AffiliateWP\Utils\DB' ) ) {
	return;
}

require_once __DIR__ . '/trait-data.php';

/**
 * Database Utilities
 *
 * @since 2.12.0
 *
 * @see Affiliate_WP_DB
 */
trait DB {

	use \AffiliateWP\Utils\Data;

	/**
	 * Inject our table name into SQL.
	 *
	 * WordPress forces you to use placeholders when you want to place
	 * `{$this->table_name}`, but if you do use placeholders it adds tickmarks
	 * around the table name. So, we have to do it this way.
	 *
	 * @see https://wordpress.stackexchange.com/questions/191729/quotes-in-table-name
	 *
	 * @since 2.12.0
	 * @since 2.12.1 Removed `has_tampered_table_name()` check, see https://github.com/awesomemotive/AffiliateWP/issues/4612.
	 *
	 * @param  string $sql SQL to inject table name into.
	 * @return string      SQL with `{table_name}` replaced with our table name.
	 *
	 * @throws \Exception                If our table name appears to be tampered with (SQL injection attempt).
	 * @throws \Exception                If you try and use this method w/out `$this::$table_name` being unset.
	 * @throws \InvalidArgumentException If `$sql` is not a string.
	 */
	private function inject_table_name( $sql ) {

		if ( ! isset( $this->table_name ) || ! $this->is_string_and_nonempty( $this->table_name ) ) {
			throw new \Exception( '$this::$table_name needs to be set to a non-empty string in order to use this method.' );
		}

		if ( ! $this->is_string_and_nonempty( $sql ) ) {
			throw new \InvalidArgumentException( '$sql must be a non-empty string.' );
		}

		global $wpdb;

		return str_replace( '{table_name}', $wpdb->_real_escape( "{$this->table_name}" ), $sql );
	}

	/**
	 * Does a table exist?
	 *
	 * @since  2.12.0
	 *
	 * @param  string $table The table in the database.
	 * @return bool
	 *
	 * @throws \InvalidArgumentException If you do not supply a non-empty string for `$table`.
	 */
	private function table_exists( $table ) {

		if ( ! $this->is_string_and_nonempty( $table ) ) {
			throw new \InvalidArgumentException( '$table must be a non-empty string.' );
		}

		global $wpdb;

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) !== $table ) {
			return false;
		}

		return true;
	}
}
