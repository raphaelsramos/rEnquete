<?php

	/***
	 *	2019-03-17
	 *	https://www.sitepoint.com/handling-post-requests-the-wordpress-way/
	 */

	namespace r\enquete;

	class CPT_Helpers {

		public static $status;
	
		public static function init(){

			CPT_Helpers::$status = [
				'missing_poll'		=> \__( 'Poll missing. Try again.', 'r-enquete' ),
				'missing_answer'	=> \__( 'Answer missing. Try again.', 'r-enquete' ),
				'invalid_nonce'		=> \__( 'Error processing your vote. Try again.', 'r-enquete' ),
				'invalid_answers'	=> \__( "We didn't find the answers associated with this poll. Try again.", 'r-enquete' ),
				'invalid_answer'	=> \__( "We didn't find the answer selected. Try again.", 'r-enquete' ),
				'already_vote'		=> \__( "You have already voted in this poll.", 'r-enquete' ),
				'success'			=> \__( 'Your vote was computed with success.', 'r-enquete' ),
			];
		
			// \add_action( 'wp_ajax_poll_vote', 'r\enquete\CPT_Helpers::process' );
			// \add_action( 'admin_post_nopriv_poll_vote', 'r\enquete\CPT_AdminPost::process' );

		}
		
		/*
		public static function get_votes_log( $poll_id ){
			$votes_json = \get_post_meta( $poll_id, 'votes', 1 );
			$votes = json_decode( $votes_json, 1 );
			if( !is_array( $votes ) ){
				$votes = [];
			}
			return $votes;
		}
		*/
		public static function get_votes( $poll_id ){
			$answers = [];
			if( $answers_json = \get_post_meta( $poll_id, 'answers', 1 ) ){
				$answers = json_decode( $answers_json, 1 );
			}
			return $answers;
		}
		
		public static function save_vote(){
			
			$r = [
				'success' => false,
				'status' => ''
			];
			
			// poll id
			if( !isset( $_POST[ 'poll' ] ) || empty( $_POST[ 'poll' ] ) ){
				$r[ 'status' ] = 'missing_poll';
				return $r;
			}

			// answer id
			if( !isset( $_POST[ 'answer' ] ) || empty( $_POST[ 'answer' ] ) ){
				$r[ 'status' ] = 'missing_answer';
			}
			
			// validate nonce
			if( !isset( $_POST[ 'poll-nonce' ] ) || !\wp_verify_nonce( $_POST[ 'poll-nonce' ], 'r-enquete-vote' ) ){
				$r[ 'status' ] = 'invalid_nonce';
				return $r;
			}

			$user = wp_get_current_user();
			
			$poll_id = intval( $_POST[ 'poll' ] );
			
			$answer_id = $_POST[ 'answer' ];
			
			// \delete_post_meta( $poll_id, 'votes' );
			
			// check votes log
			$votes = [];
			$votes_json = \get_post_meta( $poll_id, 'votes', 1 );
			if( !!$votes_json ){
				$votes = json_decode( $votes_json, 1 );
			}
			
			// if( count( $votes ) && in_array( $user->ID, $votes ) ){
			if( count( $votes ) && isset( $votes[ $user->ID ] ) ){
				$r[ 'status' ] = 'already_vote';
				return $r;
			}
			
			$answers_json = \get_post_meta( $poll_id, 'answers', true );
			
			// dont have answers to choose
			if( !$answers_json ){
				$r[ 'status' ] = 'invalid_answers';
				return $r;
			}
			
			$answers = json_decode( $answers_json, 1 );
				
			// answer_id dont exists
			if( !isset( $answers[ $answer_id ] ) ){
				$r[ 'status' ] = 'invalid_answer';
				return $r;
			}
				
			$count = (int) $answers[ $answer_id ][ 'count' ];
			$answers[ $answer_id ][ 'count' ] = ++$count;

			// update answers with count updated
			\update_post_meta( $poll_id, 'answers', json_encode( $answers, JSON_UNESCAPED_UNICODE ) );
			
			$votes[ $user->ID ] = $answer_id;
			\update_post_meta( $poll_id, 'votes', json_encode( $votes, JSON_UNESCAPED_UNICODE ) );

			return [
				'success' => true,
				'status' => 'success',
				'data' => CPT_Helpers::get_votes( $poll_id )
			];

		}
		
	}
