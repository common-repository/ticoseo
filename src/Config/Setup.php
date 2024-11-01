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

namespace Ticoseo\Config;

use Ticoseo\Common\Traits\Singleton;

/**
 * Plugin setup hooks (activation, deactivation, uninstall)
 *
 * @package Ticoseo\Config
 * @since 1.0.0
 */
final class Setup {
	/**
	 * Singleton trait
	 */
	use Singleton;

	/**
	 * Run only once after plugin is activated
	 * @docs https://developer.wordpress.org/reference/functions/register_activation_hook/
	 */
	public static function activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		/**
		 * Use this to add a database table after the plugin is activated for example
		 */

		// Clear the permalinks
		flush_rewrite_rules();

		# Uncomment the following line to see the function in action
		# exit( var_dump( $_GET ) );

		# rank math meta:
		# rank_math_focus_keyword

		// Get user settings for Posts AI
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

		if (!end($prompt_idea)) add_user_meta(get_current_user_id(), 'oai-idea-prompt', 'Write me 5 article title ideas targeting blog for golfers, write it as list with every topic on the new line, sorted by popularity', false);
		if (!end($model_idea)) add_user_meta(get_current_user_id(), 'oai-idea-model', 'text-davinci-003', true);
		if (!end($temp_idea)) add_user_meta(get_current_user_id(), 'oai-idea-temp', 0, true);
		if (!end($freq_idea)) add_user_meta(get_current_user_id(), 'oai-idea-freq', 0, true);
		if (!end($maxtokens_idea)) add_user_meta(get_current_user_id(), 'oai-idea-maxtokens', 444, true);

		if (!end($prompt_outline)) add_user_meta(get_current_user_id(), 'oai-outline-prompt', 'Write an article outlines for the title [title]', false);
		if (!end($model_outline)) add_user_meta(get_current_user_id(), 'oai-outline-model', 'text-davinci-003', true);
		if (!end($temp_outline)) add_user_meta(get_current_user_id(), 'oai-outline-temp', 0, true);
		if (!end($freq_outline)) add_user_meta(get_current_user_id(), 'oai-outline-freq', 0, true);
		if (!end($maxtokens_outline)) add_user_meta(get_current_user_id(), 'oai-outline-maxtokens', 444, true);

		if (!end($prompt_content)) add_user_meta(get_current_user_id(), 'oai-content-prompt', 'Make up extra long story based on the outlines below and the title [title], Write this story in the first case as someone who has lived it. Below are the outlines:\n\n [outlines]', false);
		if (!end($model_content)) add_user_meta(get_current_user_id(), 'oai-content-model', 'text-davinci-003', true);
		if (!end($temp_content)) add_user_meta(get_current_user_id(), 'oai-content-temp', 0, true);
		if (!end($freq_content)) add_user_meta(get_current_user_id(), 'oai-content-freq', 0, true);
		if (!end($maxtokens_content)) add_user_meta(get_current_user_id(), 'oai-content-maxtokens', 444, true);

	}

	/**
	 * Run only once after plugin is deactivated
	 * @docs https://developer.wordpress.org/reference/functions/register_deactivation_hook/
	 */
	public static function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		/**
		 * Use this to register a function which will be executed when the plugin is deactivated
		 */

		// Clear the permalinks
		flush_rewrite_rules();

		# Uncomment the following line to see the function in action
		# exit( var_dump( $_GET ) );
	}

	/**
	 * Run only once after plugin is uninstalled
	 * @docs https://developer.wordpress.org/reference/functions/register_uninstall_hook/
	 */
	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		/**
		 * Use this to remove plugin data and residues after the plugin is uninstalled for example
		 */

		# Uncomment the following line to see the function in action
		# exit( var_dump( $_GET ) );
	}
}
