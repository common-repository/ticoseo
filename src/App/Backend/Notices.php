<?php
/**
 * TicoSEO
 *
 * @package   ticoseo
 * @author    Kamil Baranek <kamil@ticoseo.com>
 * @copyright 2023 TicoSEO
 * @license   MIT
 * @link      https://ticoseo.com
 */

declare( strict_types = 1 );

namespace Ticoseo\App\Backend;

use Ticoseo\Common\Abstracts\Base;

/**
 * Class Notices
 *
 * @package Ticoseo\App\Backend
 * @since 1.0.0
 */
class Notices extends Base {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		/**
		 * This backend class is only being instantiated in the backend as requested in the Bootstrap class
		 *
		 * @see Requester::isAdminBackend()
		 * @see Bootstrap::__construct
		 *
		 * Add plugin code here for admin notices specific functions
		 */

		add_action( 'admin_notices', [ $this, 'checkAPIKEYAdminNotice' ] );
	}

	/**
	 * Missong API KEY admin notice
	 *
	 * @since 1.0.0
	 */
	public function checkAPIKEYAdminNotice() {
		global $pagenow;
		$apikey = get_option( 'oai_settings_options' );

		if ( $pagenow === 'edit.php' && !$apikey || (is_array($apikey) && $apikey['api_key'] == '') ) {
			echo '<div class="notice notice-warning is-dismissible">
	             <p>' . __( 'You need to setup API KEY for Open AI <a href="/wp-admin/options-general.php?page=ticoseo-settings">here</a>.', 'TicoSEO' ) . '</p>
	         </div>';
		}
	}
}
