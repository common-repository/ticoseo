<?php
/**
 * TicoSEO
 *
 * @package   ticoseo
 * @author    Kamil Baranek <kamil@ticoseo.com>
 * @copyright 2023 TicoSEO
 * @license   MIT
 * @link      https://ticoseo.com
 *
 * Plugin Name:     TicoSEO
 * Plugin URI:      https://ticoseo.com
 * Description:     Programmatic creation of posts with a help of ChatGPT and Open AI
 * Version:         1.0.0
 * Author:          Kamil Baranek
 * Author URI:      https://kamilbaranek.com
 * Text Domain:     ticoseo
 * Domain Path:     /languages
 * Requires PHP:    7.1
 * Requires WP:     5.5.0
 * Namespace:       Ticoseo
 */

declare( strict_types = 1 );

/**
 * Define the default root file of the plugin
 *
 * @since 1.0.0
 */
const TICOSEO_PLUGIN_FILE = __FILE__;

/**
 * Load PSR4 autoloader
 *
 * @since 1.0.0
 */
$ticoseo_autoloader = require plugin_dir_path( TICOSEO_PLUGIN_FILE ) . 'vendor/autoload.php';

/**
 * Setup hooks (activation, deactivation, uninstall)
 *
 * @since 1.0.0
 */
register_activation_hook( __FILE__, [ 'Ticoseo\Config\Setup', 'activation' ] );
register_deactivation_hook( __FILE__, [ 'Ticoseo\Config\Setup', 'deactivation' ] );
register_uninstall_hook( __FILE__, [ 'Ticoseo\Config\Setup', 'uninstall' ] );

/**
 * Bootstrap the plugin
 *
 * @since 1.0.0
 */
if ( ! class_exists( '\Ticoseo\Bootstrap' ) ) {
	wp_die( __( 'TicoSEO is unable to find the Bootstrap class.', 'ticoseo' ) );
}
add_action(
	'plugins_loaded',
	static function () use ( $ticoseo_autoloader ) {
		/**
		 * @see \Ticoseo\Bootstrap
		 */
		try {
			new \Ticoseo\Bootstrap( $ticoseo_autoloader );
		} catch ( Exception $e ) {
			wp_die( __( 'TicoSEO is unable to run the Bootstrap class.', 'ticoseo' ) );
		}
	}
);

/**
 * Create a main function for external uses
 *
 * @return \Ticoseo\Common\Functions
 * @since 1.0.0
 */
function ticoseo(): \Ticoseo\Common\Functions {
	return new \Ticoseo\Common\Functions();
}
