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
 * Class Settings
 *
 * @package Ticoseo\App\Backend
 * @since 1.0.0
 */
class Settings extends Base {

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
		 * Add plugin code here for admin settings specific functions
		 */

        // Add settings
		add_action( 'admin_menu', [ $this, 'oai_add_settings_page'] );
		add_action( 'admin_init', [ $this, 'oai_register_settings'] );

        // Add link to settings to plugin page
		add_filter( 'plugin_action_links_ticoseo/ticoseo.php', [ $this, 'ticoseo_settings_link'] );
	}

	public function ticoseo_settings_link( $links ) {
        // Build and escape the URL.
		$url = esc_url( add_query_arg(
			'page',
			'ticoseo-settings',
			get_admin_url() . 'admin.php'
		) );
        // Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
        // Adds the link to the end of the array.
		array_push(
			$links,
			$settings_link
		);
		return $links;
	}

	public function oai_add_settings_page() {
		add_options_page( 'Posts (AI)', 'Posts (AI)', 'manage_options', 'ticoseo-settings', [ $this, 'oai_render_plugin_settings_page'] );
	}

	public function oai_render_plugin_settings_page() {
		?>
		<h2>Posts AI</h2>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'oai_settings_options' );
			do_settings_sections( 'ticoseo_oai_apikey_section' ); ?>
			<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
		</form>
		<?php
	}

	public function oai_register_settings() {
		//oai_settings_options
		register_setting( 'oai_settings_options', 'oai_settings_options', [ $this, 'oai_settings_options_validate'] );
		add_settings_section( 'api_settings', 'API Settings', [ $this, 'oai_plugin_section_text'], 'ticoseo_oai_apikey_section' );

		add_settings_field( 'oai_plugin_setting_api_key', 'API Key', [ $this, 'oai_plugin_setting_api_key'], 'ticoseo_oai_apikey_section', 'api_settings' );
		// add_settings_field( 'oai_plugin_setting_results_limit', 'Results Limit', [ $this, 'oai_plugin_setting_results_limit'], 'ticoseo_oai_apikey_section', 'api_settings' );
		// add_settings_field( 'oai_plugin_setting_start_date', 'Start Date', [ $this, 'oai_plugin_setting_start_date'], 'ticoseo_oai_apikey_section', 'api_settings' );
	}

	public function oai_settings_options_validate( $input ) {
		$newinput['api_key'] = trim( sanitize_text_field($input['api_key']) );

		/*		if ( ! preg_match( '/^[a-z0-9]{51}$/i', $newinput['api_key'] ) ) {
					$newinput['api_key'] = '';
				}*/

		return $newinput;
	}

	public function oai_plugin_section_text() {
		echo '<p>Please set you Open AI api key, which you can find <a href="https://beta.openai.com/account/api-keys" target="_blank">here</a> </p>';
	}

	public function oai_plugin_setting_api_key() {
		$options = get_option( 'oai_settings_options' );
		echo "<input id='oai_plugin_setting_api_key' name='oai_settings_options[api_key]' type='text' value='" . (isset($options['api_key'])?esc_attr( $options['api_key'] ):'') . "' />";
	}

	public function oai_plugin_setting_results_limit() {
		$options = get_option( 'oai_settings_options' );
		echo "<input id='oai_plugin_setting_results_limit' name='oai_settings_options[results_limit]' type='text' value='" . (isset($options['results_limit'])?esc_attr( $options['results_limit'] ):'') . "' />";
	}

	public function oai_plugin_setting_start_date() {
		$options = get_option( 'oai_settings_options' );
		echo "<input id='oai_plugin_setting_start_date' name='oai_settings_options[start_date]' type='text' value='" . (isset($options['start_date'])?esc_attr( $options['start_date'] ):'') . "' />";
	}
}
