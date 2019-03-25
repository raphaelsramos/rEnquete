<?php

	/***
	 *	2019-03-22
	 */

	namespace r\enquete;

	class CPT_Columns {
	
		public static function init(){
				
			// column
			// https://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns
			\add_filter( 'manage_'. CPT::SLUG .'_posts_columns', 'r\enquete\CPT_Columns::order' );
			
			// https://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
			\add_filter( 'manage_'. CPT::SLUG .'_posts_custom_column', 'r\enquete\CPT_Columns::content', 10, 2 );

		}


		public static function order( $cols ){
			$cols = [
				"cb"		=> "",
				"title"		=> __( "Title" ),
				"sc"		=> __( "Shortcode", 'r-enquete' ),
				"date"		=> __( "Date" )
			];
			return $cols;
		}

		
		public function content( $column_name, $poll_id ){
			switch( $column_name ){
				case 'sc':
					echo '[enquete id="'. $poll_id .'" title="'. get_the_title( $poll_id ) .'"]';
					break;

				default:
			}
		}


	}