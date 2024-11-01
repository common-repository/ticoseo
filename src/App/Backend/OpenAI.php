<?php
/**
 * TicoSEO
 *
 * @package   ticoseo
 * @author    Kamil <kamil.baranek@me.com>
 * @copyright 2023 TicoSEO
 * @license   MIT
 * @link      https://kamilbaranek.com
 */

declare( strict_types=1 );

namespace Ticoseo\App\Backend;

use Ticoseo\Common\Abstracts\Base;


/**
 * Class Notices
 *
 * @package Ticoseo\App\Backend
 * @since 1.0.0
 */
class OpenAI extends Base {

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
		 * Open AI Page for debug
		 */
		add_action( 'admin_menu', [ $this, 'ticoseo_plugin_setup_menu' ] );

		// reorder menu > move Posts (AI) below the Posts menu
		add_filter( 'custom_menu_order', [ $this, 'reorder_admin_menu' ] );
		add_filter( 'menu_order', [ $this, 'reorder_admin_menu' ] );

		// handle the Ajax from Open AI Form
		add_action( 'wp_ajax_generate_idea', [ $this, 'generate_idea' ] );

		// handle Ajax related to IDEA stage
		add_action( 'wp_ajax_save_oai_idea_prompt', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_idea_model', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_idea_temp', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_idea_freq', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_idea_maxtokens', [ $this, 'save_oai_header_settings' ] );

		// handle Ajax related to OUTLINE stage
		add_action( 'wp_ajax_save_oai_outline_prompt', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_outline_model', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_outline_temp', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_outline_freq', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_outline_maxtokens', [ $this, 'save_oai_header_settings' ] );

		// handle Ajax related to CONTENT stage
		add_action( 'wp_ajax_save_oai_content_prompt', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_content_model', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_content_temp', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_content_freq', [ $this, 'save_oai_header_settings' ] );
		add_action( 'wp_ajax_save_oai_content_maxtokens', [ $this, 'save_oai_header_settings' ] );

		// stop heartbeat Ajax calls to reload the page (because of time-consuming REST API requests > open ai
		remove_action( 'admin_init', 'wp_auth_check_load' );

		// Insert new header section to edit oaiposts screen
		add_action('pre_get_posts', [ $this, 'oaiposts_query_add_filter'] );
		// add_filter( 'views_edit-oaipost', [ $this, "header_init" ], 10, 1 );

		// New filter rules
		add_action( 'restrict_manage_posts', [ $this, 'filter_post_by_custom_field_status'] , 10, 2);
		add_filter( 'parse_query', [ $this, 'filter_parse_query_custom_field_status'] );

		// hide Draft in a title (or other statuses)
		add_filter('display_post_states', [ $this, 'hide_post_status_in_title'], 10, 2);
	}

	function ticoseo_plugin_setup_menu() {
		// TODO: add settings menu
		/*
				add_menu_page( 'Open AI', 'Open AI', 'manage_options', 'openai-general', [ $this, 'test_init'] );
				add_submenu_page(
					'edit.php?post_type=oaipost',
					'Generate AI Post',
					'Generate AI Post',
					'manage_options',
					'oaipost-new',
					[ $this, 'test_init' ],
				);
		*/
	}

	public function my_custom_post_type() {
		return 'oaipost';
	}

	public function custom_field_status_metakey() {
		return 'oai-status';
	}

	public function custom_field_status_options() {
		return array(
			''       => __( 'All statuses', 'textdomain' ), // edit text domains
			'new'   => __( 'New', 'textdomain' ),
			'outlined' => __( 'Outlined', 'textdomain' ),
			'generated' => __( 'Generated', 'textdomain' ),
		);
	}

	public function filter_post_by_custom_field_status( $post_type, $which ) {
		if ( $this -> my_custom_post_type() === $post_type ) {
			$meta_key = $this -> custom_field_status_metakey();
			$options = $this -> custom_field_status_options();

			echo "<select name='{esc_attr($meta_key)}' id='{esc_attr($meta_key)}' class='postform'>";
			foreach ( $options as $value => $name ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr($value),
					( ( isset( $_GET[$meta_key] ) && ( $_GET[$meta_key] === $value ) ) ? ' selected="selected"' : '' ),
					esc_html($name)
				);
			}
			echo '</select>';
		}
	}

	public function filter_parse_query_custom_field_status( $query ){
		global $pagenow;

		$meta_key = $this -> custom_field_status_metakey();
		$valid_status = array_keys($this -> custom_field_status_options());
		$status = (! empty($_GET[$meta_key]) && in_array($_GET[$meta_key],$valid_status)) ? esc_attr($_GET[$meta_key]) : '';

		if ( is_admin() && 'edit.php' === $pagenow && isset($_GET['post_type']) && ($this -> my_custom_post_type()) === $_GET['post_type'] && $status ) {
			$query->query_vars['meta_key'] = $meta_key;
			$query->query_vars['meta_value'] = $status;
		}
	}

	public function hide_post_status_in_title( $states, $post ) {

		if (get_post_type($post) == 'oaipost') {
			return [];
		}

		return $states;
	}

	public function oaiposts_query_add_filter( $wp_query ) {
		if( is_admin()) {
			add_filter( 'views_edit-oaipost', [ $this, "header_init" ], 99, 1 );
		}
	}

	public function header_init( $views ) {

		// Idea:
		$prompt_idea = get_user_meta(get_current_user_id(), 'oai-idea-prompt', false);
		$model_idea = get_user_meta(get_current_user_id(), 'oai-idea-model', false);
		$temp_idea = get_user_meta(get_current_user_id(), 'oai-idea-temp', false);
		$freq_idea = get_user_meta(get_current_user_id(), 'oai-idea-freq', false);
		$maxtokens_idea = get_user_meta(get_current_user_id(), 'oai-idea-maxtokens', false);
		// Outline:
		$prompt_outline = get_user_meta(get_current_user_id(), 'oai-outline-prompt', false);
		$model_outline = get_user_meta(get_current_user_id(), 'oai-outline-model', false);
		$temp_outline = get_user_meta(get_current_user_id(), 'oai-outline-temp', false);
		$freq_outline = get_user_meta(get_current_user_id(), 'oai-outline-freq', false);
		$maxtokens_outline = get_user_meta(get_current_user_id(), 'oai-outline-maxtokens', false);
		// Content:
		$prompt_content = get_user_meta(get_current_user_id(), 'oai-content-prompt', false);
		$model_content = get_user_meta(get_current_user_id(), 'oai-content-model', false);
		$temp_content = get_user_meta(get_current_user_id(), 'oai-content-temp', false);
		$freq_content = get_user_meta(get_current_user_id(), 'oai-content-freq', false);
		$maxtokens_content = get_user_meta(get_current_user_id(), 'oai-content-maxtokens', false);


		$this->get( 'header-settings-template', null,
			[
				'class' => 'user',
				'data'  =>
					[ 'prompt_idea' => end($prompt_idea),
					  'model_idea' => end($model_idea),
					  'temp_idea' => end($temp_idea),
					  'freq_idea' => end($freq_idea),
					  'maxtokens_idea' => end($maxtokens_idea),
					  'prompt_outline' => end($prompt_outline),
					  'model_outline' => end($model_outline),
					  'temp_outline' => end($temp_outline),
					  'freq_outline' => end($freq_outline),
					  'maxtokens_outline' => end($maxtokens_outline),
					  'prompt_content' => end($prompt_content),
					  'model_content' => end($model_content),
					  'temp_content' => end($temp_content),
					  'freq_content' => end($freq_content),
					  'maxtokens_content' => end($maxtokens_content),
					],
			]
		);

	}

	public function reorder_admin_menu( $__return_true ) {
		return array(
			'index.php', // Dashboard
			'separator1', // --Space--
			'edit.php', // Posts
			'edit.php?post_type=oaipost', // AI Posts
		);
	}

	/**
	 * Retrieve a template part, modified version of:
	 * @url https://github.com/GaryJones/Gamajo-Template-Loader
	 *
	 * @param string $slug Template slug.
	 * @param string $name Optional. Template variation name. Default null.
	 * @param bool $load Optional. Whether to load template. Default true.
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function get( $slug, $name = null, $args = [], $load = true ): string {
		// Execute code for this part.
		do_action( 'get_template_part_' . $slug, $slug, $name, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'ticoseo_get_template_part_' . $slug, $slug, $name, $args );
		// Get files names of templates, for given slug and name.
		$templates = $this->getFileNames( $slug, $name, $args );

		// Return the part that is found.
		return $this->locate( $templates, $load, false, $args );
	}

	/**
	 * Given a slug and optional name, create the file names of templates, modified version of:
	 * @url https://github.com/GaryJones/Gamajo-Template-Loader
	 *
	 * @param string $slug Template slug.
	 * @param string $name Template variation name.
	 * @param $args
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function getFileNames( $slug, $name, $args ): array {
		$templates = [];
		if ( isset( $name ) ) {
			$templates[] = $slug . '-' . $name . '.php';
		}
		$templates[] = $slug . '.php';

		/**
		 * Allow template choices to be filtered.
		 *
		 * The resulting array should be in the order of most specific first, to least specific last.
		 * e.g. 0 => recipe-instructions.php, 1 => recipe.php
		 *
		 * @param array $templates Names of template files that should be looked for, for given slug and name.
		 * @param string $slug Template slug.
		 * @param string $name Template variation name.
		 *
		 * @since 1.0.0
		 *
		 */
		return apply_filters( 'ticoseo_get_template_part', $templates, $slug, $name, $args );
	}

	/**
	 * Retrieve the name of the highest priority template file that exists, modified version of:
	 * @url https://github.com/GaryJones/Gamajo-Template-Loader
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
	 * inherit from a parent theme can just overload one file. If the template is
	 * not found in either of those, it looks in the theme-compat folder last.
	 *
	 * @param string|array $template_names Template file(s) to search for, in order.
	 * @param bool $load If true the template file will be loaded if it is found.
	 * @param bool $require_once Whether to require_once or require. Default true.
	 *                                     Has no effect if $load is false.
	 * @param array $args
	 *
	 * @return string The template filename if one is located.
	 * @since 1.0.0
	 */
	public function locate( $template_names, $load = false, $require_once = true, $args = [] ): string {
		// Use $template_names as a cache key - either first element of array or the variable itself if it's a string.
		$cache_key = is_array( $template_names ) ? $template_names[0] : $template_names;
		// If the key is in the cache array, we've already located this file.
		if ( isset( $this->path_cache[ $cache_key ] ) ) {
			$located = $this->path_cache[ $cache_key ];
		} else {
			// No file found yet.
			$located = false;
			// Remove empty entries.
			$template_names = array_filter( (array) $template_names );
			$template_paths = $this->getPaths();
			// Try to find a template file.
			foreach ( $template_names as $template_name ) {
				// Trim off any slashes from the template name.
				$template_name = ltrim( $template_name, '/' );
				// Try locating this template file by looping through the template paths.
				foreach ( $template_paths as $template_path ) {
					if ( file_exists( $template_path . $template_name ) ) {
						$located = $template_path . $template_name;
						// Store the template path in the cache.
						$this->path_cache[ $cache_key ] = $located;
						break 2;
					}
				}
			}
		}
		if ( $load && $located ) {
			load_template( $located, $require_once, $args );
		}

		return $located;
	}

	/**
	 * Return a list of paths to check for template locations, modified version of:
	 * @url https://github.com/GaryJones/Gamajo-Template-Loader
	 *
	 * Default is to check in a child theme (if relevant) before a parent theme, so that themes which inherit from a
	 * parent theme can just overload one file. If the template is not found in either of those, it looks in the
	 * theme-compat folder last.
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 *
	 */
	protected function getPaths(): array {
		$theme_directory = trailingslashit( $this->plugin->extTemplateFolder() );

		$file_paths = [
			10  => trailingslashit( get_template_directory() ) . $theme_directory,
			100 => $this->plugin->templatePath(),
		];
		// Only add this conditionally, so non-child themes don't redundantly check active theme twice.
		if ( get_stylesheet_directory() !== get_template_directory() ) {
			$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
		}
		/**
		 * Allow ordered list of template paths to be amended.
		 *
		 * @param array $var Default is directory in child theme at index 1, parent theme at 10, and plugin at 100.
		 *
		 * @since 1.0.0
		 *
		 */
		$file_paths = apply_filters( 'ticoseo_template_paths', $file_paths );
		// Sort the file paths based on priority.
		ksort( $file_paths, SORT_NUMERIC );

		return array_map( 'trailingslashit', $file_paths );
	}

	public function generate_idea() {

		$apikey = get_option( 'oai_settings_options' )['api_key'];

		$model_idea = get_user_meta(get_current_user_id(), 'oai-idea-model', true);
		$temp_idea = get_user_meta(get_current_user_id(), 'oai-idea-temp', true);
		$freq_idea = get_user_meta(get_current_user_id(), 'oai-idea-freq', true);
		$maxtokens_idea = get_user_meta(get_current_user_id(), 'oai-idea-maxtokens', true);

		$client = \OpenAI::client( $apikey );

		$result = $client->completions()->create( [
			'model'       => $model_idea,
			'prompt'      => $_POST['prompt'],
			'max_tokens'  => floatval($maxtokens_idea),
			'temperature' => floatval($temp_idea),
		] );

		$listArr      = explode( "\n", $result['choices'][0]['text'] );
		$filteredList = array_filter( $listArr, fn( $value ) => ! is_null( $value ) && $value !== '' );

		$response = [];
		foreach ( $filteredList as $choice ) {
			array_push( $response, preg_replace( '/^[0-9]+\. +/', '', $choice ) );
		}

		// first should be confirmed after review on frontend
		foreach ( $response as $title ) {

			$my_post = array(
				'post_title'   => wp_strip_all_tags( $title ),
				'post_content' => 'empty',
				'post_status'  => 'draft',
				'post_type'    => 'oaipost',
				'post_author'  => 1,
				'post_name'    => $title,
			);

			kses_remove_filters();
			$post_id = wp_insert_post( $my_post );
			kses_init_filters();

			// adding tag
			wp_set_post_terms( $post_id, array( 'running' ) );
			// adding meta
			add_post_meta( $post_id, 'oai-status', 'new', true );
			add_post_meta( $post_id, 'oai-stopped', '', true );
			$task = sanitize_text_field($_POST['task']);
			add_post_meta( $post_id, 'oai-prompt', $task, false );

		}

		wp_send_json( $response );

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function save_oai_header_settings() {

		switch ($_POST['action']) {
			case 'save_oai_idea_prompt':
				$response = update_user_meta( get_current_user_id(), 'oai-idea-prompt', wp_filter_kses($_POST['value']), false );
				break;
			case 'save_oai_idea_model':
				$response = update_user_meta( get_current_user_id(), 'oai-idea-model', sanitize_text_field($_POST['value']));
				break;
			case 'save_oai_idea_temp':
				$response = update_user_meta( get_current_user_id(), 'oai-idea-temp', floatval($_POST['value']));
				break;
			case 'save_oai_idea_freq':
				$response = update_user_meta( get_current_user_id(), 'oai-idea-freq', floatval($_POST['value']));
				break;
			case 'save_oai_idea_maxtokens':
				$response = update_user_meta( get_current_user_id(), 'oai-idea-maxtokens', intval($_POST['value'] ));
				break;
			case 'save_oai_outline_prompt':
				$response = update_user_meta( get_current_user_id(), 'oai-outline-prompt', wp_filter_kses($_POST['value']),);
				break;
			case 'save_oai_outline_model':
				$response = update_user_meta( get_current_user_id(), 'oai-outline-model', sanitize_text_field($_POST['value']));
				break;
			case 'save_oai_outline_temp':
				$response = update_user_meta( get_current_user_id(), 'oai-outline-temp', floatval($_POST['value']));
				break;
			case 'save_oai_outline_freq':
				$response = update_user_meta( get_current_user_id(), 'oai-outline-freq', floatval($_POST['value']));
				break;
			case 'save_oai_outline_maxtokens':
				$response = update_user_meta( get_current_user_id(), 'oai-outline-maxtokens', intval($_POST['value']));
				break;
			case 'save_oai_content_prompt':
				$response = update_user_meta( get_current_user_id(), 'oai-content-prompt', wp_filter_kses($_POST['value']),);
				break;
			case 'save_oai_content_model':
				$response = update_user_meta( get_current_user_id(), 'oai-content-model', sanitize_text_field($_POST['value']));
				break;
			case 'save_oai_content_temp':
				$response = update_user_meta( get_current_user_id(), 'oai-content-temp', floatval($_POST['value']));
				break;
			case 'save_oai_content_freq':
				$response = update_user_meta( get_current_user_id(), 'oai-content-freq', floatval($_POST['value']));
				break;
			case 'save_oai_content_maxtokens':
				$response = update_user_meta( get_current_user_id(), 'oai-content-maxtokens', intval($_POST['value']));
				break;

		}

		wp_send_json( $response );

		wp_die(); // this is required to terminate immediately and return a proper response
	}

}
