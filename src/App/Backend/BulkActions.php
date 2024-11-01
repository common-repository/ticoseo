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
class BulkActions extends Base {

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
		 * Handle list of generated articles
		 */

		// add to dropdown
		add_filter( 'bulk_actions-edit-oaipost', [ $this, 'ticoseo_bulk_actions' ] );
		// process the action
		add_filter( 'handle_bulk_actions-edit-oaipost', [ $this, 'bulk_action_handler' ], 10, 3 );
		// display messages
		add_action( 'admin_notices', [ $this, 'bulk_action_notices' ] );
		// meta boxes
		add_action( 'add_meta_boxes', [ $this, 'add_outline_meta_box' ] );
		// browser columns
		add_filter( 'manage_oaipost_posts_columns', [ $this, 'ticoseo_custom_columns_list' ] );
		// browser custom column values
		add_action( 'manage_oaipost_posts_custom_column', [ $this, 'oaipost_post_type_custom_column_values' ], 10, 2 );
        // quick edit
		add_action( 'quick_edit_custom_box', [ $this, 'ticoseo_custom_edit_box_pt'], 10, 3 );
        // save quick edit
		add_action( 'save_post', [ $this, 'ticoseo_update_custom_quickedit_box'] );
        // bulk edit
		add_action( 'bulk_edit_custom_box', [ $this, 'ticoseo_bulk_edit_custom_box'], 10, 2 );

	}

	public function ticoseo_bulk_actions( $bulk_array ) {

		$bulk_array['create_article_outlines'] = 'Outline Article';
		$bulk_array['generate_article']         = 'Generate Article';

		return $bulk_array;

	}

	public function bulk_action_handler( $redirect, $doaction, $object_ids ) {

		// Remove query args first
		$redirect = remove_query_arg(
			array( 'create_article_outlines', 'generate_article', 'changed_post_type' ),
			$redirect
		);

		// Create Outlines
		if ( 'create_article_outlines' === $doaction ) {

			foreach ( $object_ids as $post_id ) {

				$title = get_the_title( $post_id );
				$tagToReplace = "[title]";

				$prompt_outline = get_user_meta(get_current_user_id(), 'oai-outline-prompt', false);
				$model_outline = get_user_meta(get_current_user_id(), 'oai-outline-model', false);
				$temp_outline = get_user_meta(get_current_user_id(), 'oai-outline-temp', false);
				$freq_outline = get_user_meta(get_current_user_id(), 'oai-outline-freq', false);
				$maxtokens_outline = get_user_meta(get_current_user_id(), 'oai-outline-maxtokens', false);

				$promptoutline = str_replace($tagToReplace, $title, end($prompt_outline));
                $prompt = sanitize_textarea_field($promptoutline);

				$apikey = get_option( 'oai_settings_options' )['api_key'];
				$client = \OpenAI::client($apikey);

				$result = $client->completions()->create( [
					'model'       => end($model_outline),
					'prompt'      => $prompt,
					'max_tokens'  => floatval(end($maxtokens_outline)),
					'temperature' => floatval(end($temp_outline)),
				] );

				$listArr  = explode( "\n", $result['choices'][0]['text'] );
				// $outlines = array_filter( $listArr, fn( $value ) => ! is_null( $value ) && $value !== '' );

				// adding meta
                $outlines = sanitize_textarea_field($result['choices'][0]['text']);
				add_post_meta( $post_id, 'article-' . $post_id . '-outlines', $outlines, true );

				update_post_meta( $post_id, 'oai-status', 'outlined');

                $stopped = sanitize_text_field($result['choices'][0]['finishReason']);
                update_post_meta( $post_id, 'oai-stopped', $stopped);

                add_post_meta($post_id, 'oai-prompt', $prompt, false);

				wp_update_post(
					array(
						'ID'           => $post_id,
						'post_content' => $outlines
						// for now > later I will handle it somewhere else like meta
					)
				);
			}

			// Add query args to URL because I will show notices later
			$redirect = add_query_arg(
				'create_article_outlines', // just a parameter for URL
				count( $object_ids ), // how many posts have been selected
				$redirect
			);

		}

		// Finish Articles
		if ( 'generate_article' === $doaction ) {
			foreach ( $object_ids as $post_id ) {

				$title = get_the_title( $post_id );
				$titleTagToReplace = "[title]";
				$outlineTagToReplace = "[outline]";

				$prompt_content = get_user_meta(get_current_user_id(), 'oai-content-prompt', false);
				$model_content = get_user_meta(get_current_user_id(), 'oai-content-model', false);
				$temp_content = get_user_meta(get_current_user_id(), 'oai-content-temp', false);
				$freq_content = get_user_meta(get_current_user_id(), 'oai-content-freq', false);
				$maxtokens_content = get_user_meta(get_current_user_id(), 'oai-content-maxtokens', false);

				$outlines = get_post_meta( $post_id, 'article-' . $post_id . '-outlines', true );

				if ( $outlines != '' || ($outlines == '' && !str_contains(end($prompt_content), '[outline]'))) {

                    $prePrompt = str_replace($titleTagToReplace, $title, end($prompt_content));
					$promptTemp = str_replace($outlineTagToReplace, $outlines, $prePrompt);
					$prompt = sanitize_textarea_field($promptTemp);

					$apikey = get_option( 'oai_settings_options' )['api_key'];
					$client = \OpenAI::client($apikey);

					try{

						$response = $client->completions()->create( [
							'model'       => end($model_content),
							'prompt'      => $prompt,
							'max_tokens'  => floatval(end($maxtokens_content)),
							'temperature' => floatval(end($temp_content)),
						] );

					} catch (\Exception $ex) {
						$error_message = $ex->getMessage();
						add_settings_error('article_generation_error', '', $error_message, 'error');
						settings_errors( 'article_generation_error' );
					}

					foreach ($response->choices as $result) {

						update_post_meta( $post_id, 'oai-status', 'generated' );

                        $stopped = sanitize_text_field($result->finishReason);
						update_post_meta( $post_id, 'oai-stopped', $stopped );
						add_post_meta($post_id, 'oai-prompt', $prompt, false);

                        // $postcontent = print_r($this->getLongTailKeywords($result->text),true).'\n\n';
                        $postcontent.= sanitize_textarea_field($result->text);

						wp_update_post(
							array(
								'ID'           => $post_id,
								'post_content' => $postcontent
							)
						);
					}

				}

			}

			$redirect = add_query_arg(
				'generate_article',
				count( $object_ids ),
				$redirect
			);
		}

        // Update post types
		if ( 'change_post_type' === $doaction ) {

			foreach ( $object_ids as $post_id ) {

				if( isset( $_POST ) && isset( $_POST['change_post_type'] ) ) {
					set_post_type($post_id, $_POST['change_post_type']);
				}

			}

			// do not forget to add query args to URL because we will show notices later
			$redirect = add_query_arg(
				'changed_post_type', // just a parameter for URL
				count( $object_ids ), // how many posts have been selected
				$redirect
			);

		}

		return $redirect;

	}

	public function bulk_action_notices() {

		// Outlines: simple notice message
		if ( ! empty( $_REQUEST['create_article_outlines'] ) ) {
			?>
            <div class="updated notice is-dismissible">
                <p>Posts outlined!</p>
            </div>
			<?php
		}

		// Generated "rich" message
		if ( ! empty( $_REQUEST['generate_article'] ) ) {

			$count = (int) $_REQUEST['generate_article'];
			// depending on ho much posts were changed, make the message different
			$message = sprintf(
				_n(
					'Content of %d article has been changed.',
					'Content of %d articles has been changed.',
					$count
				),
				$count
			);
			echo "<div class=\"updated notice is-dismissible\"><p>{esc_attr($message)}</p></div>";
		}

		if ( ! empty( $_REQUEST['changed_post_type'] ) ) {
			?>
            <div class="updated notice is-dismissible">
                <p>Post types updated!</p>
            </div>
			<?php
		}



	}

	function add_outline_meta_box() {

		$screens = array( 'oaipost' );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'article_generate_prompt_box',
				__( 'Prompt used for generating article', 'TicoSEO' ),
				[
					$this,
					'generate_prompt_meta_box_callback'
				],
				$screen
			);


			// https://codex.wordpress.org/Function_Reference/add_meta_box - add_meta_box(), see for further params
			add_meta_box(
				'article_outlines_box',                           // HTML 'id' attribute of the edit screen section
				__( 'Article outlines', 'TicoSEO' ),   // Title of the edit screen section, visible to user
				[
					$this,
					'outlines_meta_box_callback'
				],    // Function that prints out the HTML for the edit screen section.
				$screen                                           // Which writing screen ('post','page','dashboard','link','attachment','custom_post_type','comment')
			);

		}
	}

	/**
	 * Prints the box content.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	function outlines_meta_box_callback( $post, $box ) {

		// Add a nonce field so I can check for it later.
		wp_nonce_field( 'outlines_meta_box_data', 'outlines_meta_box_nonce' );

		$alloutlines = get_post_meta( $post->ID, 'article-'.$post->ID.'-outlines' );
        $outlines = end($alloutlines);

		echo '<pre>' . print_r( esc_textarea($outlines), true ) . '</pre>';

		// Don't forget about this, otherwise you will mess up with other data on the page
		wp_reset_postdata();

	}

	/**
	 * Prints the box content.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	function generate_prompt_meta_box_callback( $post, $box ) {

		wp_nonce_field( 'generate_prompt_meta_box_data', 'generate_prompt_meta_box_nonce' );

		$metas = get_post_meta( $post->ID, 'oai-prompt');
		$promtps = end($metas);

		echo '<p>' . esc_textarea($promtps) . '</p>';


		// Don't forget about this, otherwise you will mess up with other data on the page
		wp_reset_postdata();

	}

	function getLongTailKeywords($str, $len = 3, $min = 2){ $keywords = array();
		$common = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www');
		$str = preg_replace('/[^a-z0-9\s-]+/', '', strtolower(strip_tags($str)));
		$str = preg_split('/\s+-\s+|\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);
		while(0<$len--) for($i=0;$i<count($str)-$len;$i++){
			$word = array_slice($str, $i, $len+1);
			if(in_array($word[0], $common)||in_array(end($word), $common)) continue;
			$word = implode(' ', $word);
			if(!isset($keywords[$len][$word])) $keywords[$len][$word] = 0;
			$keywords[$len][$word]++;
		}
		$return = array();
		foreach($keywords as &$keyword){
			$keyword = array_filter($keyword, function($v) use($min){ return !!($v>$min); });
			arsort($keyword);
			$return = array_merge($return, $keyword);
		}
		return $return;
	}

	public function ticoseo_custom_columns_list( $columns ) {

		$custom_col_order = array(
			'cb'         => $columns['cb'],
			'title'      => $columns['title'],
			'oai_status' => __( 'Status', 'TicoSEO' ),
			'oai_words' => __( 'Words', 'TicoSEO' ),
			'oai_stopped' => __( 'Stopped', 'TicoSEO' ),
			'date'       => $columns['date']
		);

		return $custom_col_order;
	}

	public function oaipost_post_type_custom_column_values( $column, $post_id ) {
	    switch ( $column ) {
		    case 'cb'	:
		    case 'title' 	:
		    case 'oai_status' 	:
			    $status = get_post_meta( $post_id , 'oai-status' , true );
                echo ($status?esc_attr($status):'<div class="dashicons dashicons-minus"></div>');
                break;
		    case 'oai_words' 	:
			    $content = get_the_content( $post_id );
			    $word_count = str_word_count(trim(strip_tags($content)));
			    echo esc_attr($word_count);
			    break;
		    case 'oai_stopped' 	:
			    $stopped = get_post_meta( $post_id , 'oai-stopped' , true );
			    echo esc_attr($stopped);
			    break;
        }
    }

    public function ticoseo_custom_edit_box_pt( $column_name, $post_type, $taxonomy ) {
	    global $post;

	    switch ( $post_type ) {
		    case 'oaipost':

			    if( $column_name === 'oai_words' ):
				    ?>
                    <fieldset class="inline-edit-col-left">
                        <div class="inline-edit-col">
                            <span class="title">Post Type</span>
                            <input type="hidden" name="post_type_noncename" id="post_type_noncename" value="" />
						    <?php
						    $args       = array(
							    'public' => true,
						    );
						    $posttypes = get_post_types($args, 'objects');
						    ?>
                            <select name='change_post_type' id='change_post_type'>
							    <?php
							    foreach ($posttypes as $posttype) {
                                    $selected = (get_post_type( $post->ID ) == $posttype->name )? 'selected' : '';
								    echo "<option class='change_post_type-option' value='{esc_attr($posttype->name)}' {esc_attr($selected)}>{esc_attr($posttype->label)}</option>\n";
							    }
							    ?>
                            </select>
                        </div>
                    </fieldset>
			    <?php
			    endif;
			    break;
		    default:
			    break;
	    }

    }

	public function ticoseo_update_custom_quickedit_box() {
		// checking logic here, ajax save and so on

		if( isset( $_POST ) && isset( $_POST['change_post_type'] ) ) {
            $change_post_type = sanitize_text_field($_POST['change_post_type']);
            $postID = intval($_POST['post_ID']);
			set_post_type($postID, $change_post_type);
		}

		if( isset( $_REQUEST ) && isset( $_REQUEST['change_post_type'] ) && isset( $_REQUEST['bulk_edit'] ) ) {
            $post_ids = $_REQUEST['post'];
			foreach ( $post_ids as $post_id ) {
				$postID = intval($post_id);
				$change_post_type = sanitize_text_field($_REQUEST['change_post_type']);
                set_post_type($postID, $change_post_type);
			}
		}

		return;
	}

    public function ticoseo_bulk_edit_custom_box( $column_name, $post_type ) {
        if ( $column_name != 'oai_words' || $post_type != 'oaipost' ) {
            return;
        }

    ?>
        <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
                <span class="title">Post Type</span>
                <input type="hidden" name="post_type_noncename" id="post_type_noncename" value="" />
			    <?php
			    $args       = array(
				    'public' => true,
			    );
			    $posttypes = get_post_types($args, 'objects');
			    ?>
                <select name='change_post_type' id='change_post_type'>
				    <?php
				    foreach ($posttypes as $posttype) {
					    $selected = (get_post_type( $post->ID ) == $posttype->name )? 'selected' : '';
					    echo "<option class='change_post_type-option' value='{esc_attr($posttype->name)}' {esc_attr($selected)}>{esc_attr($posttype->label)}</option>\n";
				    }
				    ?>
                </select>
            </div>
        </fieldset>
        <?php
    }
}
