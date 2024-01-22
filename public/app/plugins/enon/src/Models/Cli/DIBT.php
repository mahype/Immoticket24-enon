<?php

namespace Enon\Models\Cli;

use WPENON\Model\Energieausweis;
use WPENON\Model\EnergieausweisXML;

/**
 * DIBT mass function for CLI.
 *
 * @since 1.0.0
 */
class DIBT extends \WP_CLI_Command {
	/**
	 * Scrub posts.
	 *
	 * OPTIONS
	 *
	 * --date=<date>
	 * : Test XML until this date.
	 *
	 * [--xsd=<xsd_schema>]
	 * : XSD Schema. Default: https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd
	 * 
	 * [--version=<xsd_version>]
	 * : XSD version. Default: 'GEG-2023'
	 * 
	 * [--schema=<schema_name>]
	 * : Schema name. Default: 'enev2022-01'
	 *
	 * ## EXAMPLES
	 *
	 *     wp dibt schematest --date='-1 month' --xsd='https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2023_V1_0.xsd' --version='GEG-2023'
	 *
	 * @param array $args       WP CLI arguments.
	 * @param array $assoc_args WP CLI associated arguments.
	 *
	 * @since 1.0.0
	 */
	public function schematest( $args, $assoc_args ) {
		$date    = gmdate( 'Y-m-d', strtotime( $assoc_args['date'] ) );
		libxml_use_internal_errors(true);

		$args   = array(
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'posts_per_page'         => -1,
			'post_type'              => 'download',
			'post_status'            => 'publish',
			'date_query'             => array(
				array(
					'after' => $date,
				),
			),
		);

		if( isset($assoc_args['xsd']) ) {
			$xsd = $assoc_args['xsd'];
		} else {
			$xsd = 'https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd';
		}

		if( isset($assoc_args['version']) ) {
			$version = $assoc_args['version'];
		} else {
			$version = 'GEG-2020';
		}

		if( isset($assoc_args['schema_name']) ) {
			$schema_name = $assoc_args['schema_name'];
		} else {
			$schema_name = 'enev2024-01';
		}

		define('GEG_XSD', $xsd);
		define('GEG_XSD_VERSION', $version);

		$post_ids = get_posts($args); 

		$working_dir = dirname( dirname( ABSPATH ) ) . '/tmp/';

		$log_file = WP_LOG_DIR . '/dibt-schematest-' . $version . '.log';

		@unlink($log_file);

		$log = fopen( $log_file, 'w' );

		if( ! is_dir($working_dir) ) {
			mkdir($working_dir);
		}

		$xsd_file = dirname( dirname( ABSPATH ) ) . '/tmp/' . basename($xsd);
		file_put_contents($xsd_file, file_get_contents($xsd));

		foreach($post_ids AS $post_id) {
			$energy_certificate = new Energieausweis($post_id);

			if( $energy_certificate->schema_name !== $schema_name ) {
				\WP_CLI::line( 'Skipping ' . $energy_certificate->post_title . ' because of other schema.' );
				continue;
			} elseif ( ! $energy_certificate->isFinalized()  ) {
				\WP_CLI::line( 'Skipping ' . $energy_certificate->post_title . ' because certificate is not finalized.' );
				continue;
			}else {
				\WP_CLI::line( 'Checking ' . $energy_certificate->post_title . '...' );
			}

			$xml = $energy_certificate->getXML('zusatzdatenerfassung', 'S', false);
			$xml_file = $working_dir . $energy_certificate->post_title . '.xml';
			file_put_contents($xml_file, $xml);

			$xmlDoc = new \DOMDocument();
			$xmlDoc->load($xml_file);

			if( $xmlDoc->schemaValidate($xsd_file) ) {
				\WP_CLI::line( 'XML is valid for ' . $energy_certificate->post_title );
			} else {
				\WP_CLI::line( 'XML is invalid for ' . $energy_certificate->post_title );

				fwrite($log, $energy_certificate->post_title . PHP_EOL);

				foreach(libxml_get_errors() as $error) {
					\WP_CLI::line( $error->message );
					fwrite($log, $error->message);
				}
			}

			unlink($xml_file);
		}

		fclose($log);

		unlink($xsd_file);
		unlink($working_dir);
		\WP_CLI::success( 'Done!' );
	}
}
