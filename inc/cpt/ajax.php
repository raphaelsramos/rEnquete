<?php

	/***
	 *	2019-03-17
	 *	https://www.sitepoint.com/handling-post-requests-the-wordpress-way/
	 */

	namespace r\enquete;

	class CPT_Ajax {

		public static function init(){

			\add_action( 'wp_ajax_poll_vote', 'r\enquete\CPT_Ajax::process' );
			// \add_action( 'admin_post_nopriv_poll_vote', 'r\enquete\CPT_AdminPost::process' );

		}
		
		
		public static function process(){
			$process = CPT_Helpers::save_vote();
			\wp_send_json( $process );
			die( $process[ 'status' ] );
		}
		
	}
