<?php

	/***
	 *	2019-03-17
	 *	https://www.sitepoint.com/handling-post-requests-the-wordpress-way/
	 */

	namespace r\enquete;

	class CPT_AdminPost {

		public static function init(){

			\add_action( 'admin_post_poll_vote', 'r\enquete\CPT_AdminPost::process' );
			// \add_action( 'admin_post_nopriv_poll_vote', 'r\enquete\CPT_AdminPost::process' );

		}
		
		
		public static function process(){
			
			$referer = \esc_url( $_POST[ 'referer' ] );
			
			// poll id
			if( !isset( $_POST[ 'poll' ] ) || empty( $_POST[ 'poll' ] ) ){
				\wp_redirect( $referer .'?poll-error=missing_poll' );
				die( 'error=missing_poll' );
			}
			
			$process = CPT_Helpers::save_vote();
			if( !$process[ 'success' ] ){
				\wp_redirect( $referer .'?poll-error='. $process[ 'status' ] );
				die( 'error='. $process[ 'status' ] );
			}
			
			\wp_redirect( $referer .'?poll-success=true' );
			die( 'success=true' );
		}

		/***
		public static function process(){
			
			$referer = \esc_url( $_POST[ 'referer' ] );
			
			// poll id
			if( !isset( $_POST[ 'poll' ] ) || empty( $_POST[ 'poll' ] ) ){
				\wp_redirect( $referer .'?poll-error=missing_poll' );
				die( 'missing_poll' );
			}

			// answer id
			if( !isset( $_POST[ 'answer' ] ) || empty( $_POST[ 'answer' ] ) ){
				\wp_redirect( $referer .'?poll-error=missing_answer' );
				die( 'missing_answer' );
			}
			
			// validate nonce
			if( !isset( $_POST[ 'poll-nonce' ] ) || !\wp_verify_nonce( $_POST[ 'poll-nonce' ], 'r-enquete-vote' ) ){
				\wp_redirect( $referer .'?poll-error=invalid_nonce' );
				die( 'invalid_nonce' );
			}

			$user = wp_get_current_user();
			
			$poll_id = intval( $_POST[ 'poll' ] );
			
			$answer_id = $_POST[ 'answer' ];
			
			// check votes log
			// \delete_post_meta( $poll_id, 'votes' );
			// \delete_post_meta( $poll_id, 'votes_json' );
			
			#$votes = \get_post_meta( $poll_id, 'votes', 1 );
			#if( !is_array( $votes ) ){
			#	$votes = [];
			#}
			
			/*
			$votes_json = \get_post_meta( $poll_id, 'votes_json', 1 );
			$votes_json = json_decode( $votes_json, 1 );
			if( !is_array( $votes_json ) ){
				$votes_json = [];
			}
			*
			
			$votes_json = \get_post_meta( $poll_id, 'votes', 1 );
			$votes = json_decode( $votes_json, 1 );
			if( !is_array( $votes ) ){
				$votes = [];
			}

			// if( count( $votes ) && isset( $votes[ $user->ID ] ) ){
			if( count( $votes ) && in_array( $user->ID, $votes ) ){
				\wp_redirect( $referer .'?poll-error=already_vote' );
				die( 'already_vote' );
			}
			
			$answers_json = \get_post_meta( $poll_id, 'answers', true );
			
			// dont have answers to choose
			if( !$answers_json ){
				\wp_redirect( $referer .'?poll-error=invalid_answers' );
				die( 'invalid_poll_answers' );
			}
			
			$answers = json_decode( $answers_json, 1 );
				
			// answer_id dont exists
			if( !isset( $answers[ $answer_id ] ) ){
				\wp_redirect( $referer .'?poll-error=invalid_answer' );
				die( 'invalid_answer_id' );
			}
				
			$count = (int) $answers[ $answer_id ][ 'count' ];
			$answers[ $answer_id ][ 'count' ] = ++$count;

			// update answers with count updated
			\update_post_meta( $poll_id, 'answers', json_encode( $answers, JSON_UNESCAPED_UNICODE ) );
			
			// $votes[ $user->ID ] = $answer_id;
			#$votes[] = $user->ID;
			#\update_post_meta( $poll_id, 'votes', $votes );
			
			// $votes_json[ $user->ID ] = $user->ID;
			// $votes_json[] = $user->ID;
			// \update_post_meta( $poll_id, 'votes_json', json_encode( $votes_json, JSON_UNESCAPED_UNICODE ) );
			$votes[] = $user->ID;
			\update_post_meta( $poll_id, 'votes', json_encode( $votes, JSON_UNESCAPED_UNICODE ) );

			\wp_redirect( $referer .'?poll-success=true' );
			die( 'success=true' );

		}
		*/
		
	}
