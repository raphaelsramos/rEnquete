<?php

	/***
	 *	2019-03-19
	 *
	 *	REFS:
	 *		- https://allisontarr.com/2017/11/17/custom-meta-boxes-media/
	 *		- https://gist.github.com/cferdinandi/86f6e326b30b8b5416c0a7e43271efa6
	 *		- https://code.tutsplus.com/articles/attaching-files-to-your-posts-using-wordpress-custom-meta-boxes-part-1--wp-22291
	 *
	 */

	namespace r\enquete;
	
	class CPT_Metabox {

		public static function init(){

			\add_action( 'admin_enqueue_scripts', 'r\enquete\CPT_Metabox::enqueues', 10, 1 );
			
			// add metabox
			\add_action( 'add_meta_boxes', 'r\enquete\CPT_Metabox::add' );
			
			\add_action( 'save_post', 'r\enquete\CPT_Metabox::save' );
			

		}

		
		public static function enqueues( $hook_suffix ){
			// global $typenow;
			// if( $typenow == CPT::SLUG ){
			if( in_array( $hook_suffix, [ 'post.php', 'post-new.php' ] ) ){
				$screen = \get_current_screen();
				
				/*
				\var_dump( [
					'screen' => $screen,
					'CPT::SLUG' => CPT::SLUG,
				] ); 
				*/
				 
				if( \is_object( $screen ) && CPT::SLUG == $screen->post_type ){

					// Registers and enqueues the required javascript.
					\wp_register_script( 'r/enquete/cpt/metabox', URL .'assets/js/admin/cpt/metabox.js', [ 'jquery' ] );
				
					/*
					wp_localize_script( 'r/enquete/cpt/metabox', 'enquete_metabox',
						array(
							'title' => __( 'Choose or Upload File', 'extranet-seven-capital' ),
							'button' => __( 'Use this file', 'extranet-seven-capital' ),
						)
					);
					*/
					\wp_enqueue_script( 'r/enquete/cpt/metabox' );
					
					\wp_enqueue_style( 'r/enquete/cpt/metabox', URL .'assets/css/admin/cpt/metabox.css' );
				}
			}
		}

		
		public static function add() {
			\add_meta_box( 
				'r/enquete/cpt/metabox',			// id
				\__( 'Answers', 'r-enquete' ),		// title
				'r\enquete\CPT_Metabox::render',	// cb
				CPT::SLUG,							// screen
				'normal'							// context
			) ;
		}


		// Register Custom Post Type
		public static function render( $post ){
?>
			<table id="polls-options">
				<thead>
					<tr>
						<th scope="col">#</th>
						<th scope="col"><?php _e( 'ID', 'r-enquete' ) ?></th>
						<th scope="col"><?php _e( 'Answer', 'r-enquete' ) ?></th>
						<th scope="col"><?php _e( 'Count', 'r-enquete' ) ?></th>
						<th scope="col"><?php _e( 'Actions', 'r-enquete' ) ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col">#</th>
						<th scope="col"><?php _e( 'ID', 'r-enquete' ) ?></th>
						<th scope="col"><?php _e( 'Answer', 'r-enquete' ) ?></th>
						<th scope="col"><?php _e( 'Count', 'r-enquete' ) ?></th>
						<th scope="col"><?php _e( 'Actions', 'r-enquete' ) ?></th>
					</tr>
				</tfoot>
				<tbody id="polls">
<?php
			if( $answers_json = \get_post_meta( $post->ID, 'answers', true ) ){
				
				$answers = json_decode( $answers_json, 1 );
				
				/*
				echo "<pre>";
				var_dump( [
					'answers_json' => $answers_json,
					'answers' => $answers,
				] );
				echo "</pre>";
				*/
				
				foreach( $answers as $answer ){
					$id = $answer[ 'id' ];
?>
					<tr class="poll-item" id="poll-item-<?php echo $id ?>">
						<th scope="row" class="poll-item-num"></th>
						<td class="poll-item-id">
							<input type="hidden" name="poll_answers[<?php echo $id ?>][id]" value="<?php echo $id ?>" /><?php echo $id ?>
						</td>
						<td class="poll-item-answer">
							<textarea name="poll_answers[<?php echo $id ?>][answer]" id="poll_answers_<?php echo $id ?>_answer" class="text"><?php echo $answer[ 'answer' ] ?></textarea>
						</td>
						<td class="poll-item-count">
							<input type="text" name="poll_answers[<?php echo $id ?>][count]" id="poll_answers_<?php echo $id ?>_count" value="<?php echo $answer[ 'count' ] ?>" class="text disabled" />
						</td>
						<td class="poll-item-actions">
							<a href="#" data-id="<?php echo $id ?>" title="<?php _e( 'Remove this Answer', 'r-enquete' ) ?>" class="button poll-item-remove">&times;</a>
						</td>
					</tr>
<?php
				}
			}
?>
				</tbody>
			</table>			
			<button id="polls-add-answer" title="<?php _e( 'Remove this Answer', 'r-enquete' ) ?>" class="button button-primary">&plus; <?php _e( 'Add Answer', 'r-enquete' ) ?></button>
			
			<hr />
			
			<details>
				<summary><?php _e( 'Log of Votes', ' r-enquete' ) ?></summary>
				<pre style="max-height: 480px; overflow: auto">
<?php
			\delete_post_meta( $post->ID, 'votes' );
			var_dump( json_decode( \get_post_meta( $post->ID, 'votes', 1 ), 1 ) );
?>
				</pre>
			</details>

			<template id="poll-item-tmpl">
					<tr class="poll-item" id="poll-item-{id}">
						<th scope="row" class="poll-item-num"></th>
						<td class="poll-item-id">
							<input type="hidden" name="poll_answers[{id}][id]" value="{id}" />{id}
						</td>
						<td class="poll-item-answer">
							<textarea name="poll_answers[{id}][answer]" id="poll_answers_{id}_answer" class="text"></textarea>
						</td>
						<td class="poll-item-count">
							<input type="text" name="poll_answers[{id}][count]" id="poll_answers_{id}_count" value="0" class="text disabled" />
						</td>
						<td class="poll-item-actions">
							<a href="#" data-id="{id}" title="<?php _e( 'Remove this Answer', 'r-enquete' ) ?>" class="button poll-item-remove">&times;</a>
						</td>
					</tr>
			</template>
<?php
			// Security field
			// wp_nonce_field( 'value/action', 'field_name' );
			\wp_nonce_field( 'r/enquete/cpt/metabox', 'r_enq_mtbx_nonce' );
			#echo "<pre>";
			#var_dump( json_decode( \get_post_meta( $post->ID, 'votes', 1 ), 1 ) );
			#echo "</pre>";
		}
		
		
		public static function save( $id ) {
			if( !empty( $_POST[ 'poll_answers' ] ) ){
				\update_post_meta( $id, 'answers', json_encode( $_POST[ 'poll_answers' ], JSON_UNESCAPED_UNICODE ) );
			}
		}
		
		// https://gist.github.com/gordonbrander/2230317 for js version
		/*
		public static function gen_id( $length ){
			$key = '';
			$keys = array_merge( range( 0, 9 ), range( 'a', 'z' ) );

			for( $i = 0; $i < $length; $i++ ){
				$key .= $keys[ array_rand( $keys ) ];
			}
			return $key;
		}
		*/

	}
