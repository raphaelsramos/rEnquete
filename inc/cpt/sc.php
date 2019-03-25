<?php

	/***
	 *	2019-03-22
	 */

	namespace r\enquete;
	
	class CPT_SC {
	
		public static function init(){
			
			#var_dump( 'Extranet_SC_Files_SC::init' );
			
			// add
			// \add_shortcode( 'poll',		'r\enquete\CPT_SC::render' );
			\add_shortcode( 'enquete',	'r\enquete\CPT_SC::render' );
			
			// enqueue files
			\add_action( 'wp_enqueue_scripts', 'r\enquete\CPT_SC::enqueues' );

		}


		public static function enqueues() {
			\wp_enqueue_style(
				NAME .'-cpt-sc',
				URL .'assets/css/cpt/sc.css',
				[],
				VERSION,
				'all'
			);

			\wp_register_script(
				NAME .'-cpt-sc',
				URL .'assets/js/cpt/sc.js',
				[ 'jquery' ],
				VERSION,
				true
			);
			\wp_localize_script( NAME .'-cpt-sc', '$polls', [
				'ajax_url'			=> admin_url( 'admin-ajax.php' ),
				'missing_poll'		=> \__( 'Poll missing. Try again.', 'r-enquete' ),
				'missing_answer'	=> \__( 'Answer missing. Try again.', 'r-enquete' ),
				'invalid_nonce'		=> \__( 'Error processing your vote. Try again.', 'r-enquete' ),
				'invalid_answers'	=> \__( "We didn't find the answers associated with this poll. Try again.", 'r-enquete' ),
				'invalid_answer'	=> \__( "We didn't find the answer selected. Try again.", 'r-enquete' ),
				'already_vote'		=> \__( "You have already voted in this poll.", 'r-enquete' ),
				'success'			=> \__( 'Your vote was computed with success.', 'r-enquete' ),
				'select_answer'		=> \__( 'Please choose one answer.', 'r-enquete' ),
			] );
			\wp_enqueue_script( NAME .'-cpt-sc' );
		}


		public static function messages(){
			$msg = '';
			if( isset( $_GET[ 'poll-error' ] ) ){
				switch( $_GET[ 'poll-error' ] ){
					
					case 'missing_poll':
						$msg = \__( 'Poll missing. Try again.', 'r-enquete' );
						break;
					
					case 'missing_answer':
						$msg = \__( 'Answer missing. Try again.', 'r-enquete' );
						break;
					
					case 'invalid_nonce':
						$msg = \__( 'Error processing your vote. Try again.', 'r-enquete' );
						break;
					
					case 'invalid_answers':
						$msg = \__( "We didn't find the answers associated with this poll. Try again.", 'r-enquete' );
						break;
					
					case 'invalid_answer':
						$msg = \__( "We didn't find the answer selected. Try again.", 'r-enquete' );
						break;
					
					case 'already_vote':
						$msg = \__( "You have already voted in this poll.", 'r-enquete' );
						break;
				}
				return implode( "\n", [
					'<div class="alert error">',
						'<div class="alert-content">',
							$msg,
						'</div>',
						'<a href="#" class="alert-remove">&times;</a>',
					'</div>'
				] );
			}
			elseif( isset( $_GET[ 'poll-success' ] ) && $_GET[ 'poll-success' ] == 'true' ){
				return implode( "\n", [
					'<div class="alert success">',
						'<div class="alert-content">',
							\__( 'Your vote was computed with success.', 'r-enquete' ),
						'</div>',
						'<a href="#" class="alert-remove">&times;</a>',
					'</div>'
				] );
			}
		}
			
		public static function render( $atts ){
			
			if( !isset( $atts[ 'id' ] ) ){
				return '[poll error="missing id"]';
			}
			
			$poll = get_post( $atts[ 'id' ] );
			
			if( !$poll ){
				return '[poll error="invalid id"]';
			}

			$args = array_merge( [
				'id' => 0,
			], $atts );
			
			
			#$votes = \get_post_meta( $poll->ID, 'votes', 1 );
			#echo "<pre>";
			#var_dump( [
			#	'votes' => $votes,
			#] );
			#echo "</pre>";

			global $wp;
			$current_url = home_url( add_query_arg( [], $wp->request ) );

			$html = [
						'<div id="poll-'. $poll->ID .'" class="poll">',
							'<h3 class="poll-title">'. $poll->post_title .'</h3>',
							CPT_SC::messages(),
							'<form class="poll-form" action="'. esc_url( admin_url( 'admin-post.php' ) ) .'" method="post">',
								'<input type="hidden" name="action" value="poll_vote" />',
								'<input type="hidden" name="poll" value="'. $poll->ID .'" />',
								'<input type="hidden" name="referer" value="'. $current_url .'" />',
									wp_nonce_field( 'r-enquete-vote', 'poll-nonce', 0, 0 ),
								'<ul class="poll-options">',
			];
			
			
			$answers_json = \get_post_meta( $poll->ID, 'answers', true );
			$answers = [];
			$total = 0;
			
			if( !!$answers_json ){
				$answers = json_decode( $answers_json, 1 );
			}
			
			if( count( $answers ) ){
				foreach( $answers as $answer ){
					
					$item = implode( "\n", [
									'<li class="poll-option">',
										'<label>',
											'<input type="radio" name="answer" id="answer_'. $answer[ 'id' ] .'" value="'. $answer[ 'id' ] .'" />',
											'<span>'. $answer[ 'answer' ] .'</span>',
										'</label>',
									'</li>'
					] );
					
					$total += (int) $answer[ 'count' ];
					
					$item = \apply_filters( 'r/enquete/cpt/sc/item', $item, $answer );
					
					$html[] = $item;
					
				}
				
			}
			
			$html = array_merge( $html, [
								'</ul>',
								'<div class="poll-actions">',
									'<a href="#" class="poll-ranking-show button outline">'. \__( 'See statistics', 'r-enquete' ) .'</a>',
									'<input type="submit" class="submit poll-submit" value="'. \__( 'Vote', 'r-enquete' ) .'" />',
								'</div>',
							'</form>',
							'<div class="poll-ranking">',
								'<ul class="poll-graphs" data-total="'. $total .'">'
			] );
			
			if( count( $answers ) ){
				foreach( $answers as $answer ){
					
					$count = (int) $answer[ 'count' ];
					$percentual = 0;
					if( $count ){
						$percentual = number_format( ( $count / $total ) * 100, 2 );
					}
					
					$item = implode( "\n", [
										'<li class="poll-graph" data-count="'. $count .'">',
											'<div class="poll-answer">'. $answer[ 'answer' ] .': ',
												'<span class="votes">'. $count .' '. \__( 'votes', 'r-enquete' ) .'</span> /',
												'<span class="percentual">'. $percentual .'%</span>',
											'</div>',
											'<div class="poll-bar-bg"><div class="poll-bar" style="width: '. $percentual .'%"></div></div>',
										'</li>'
					] );
					
					$item = \apply_filters( 'r/enquete/cpt/sc/item', $item, $answer );
						
					$html[] = $item;
					
				}
				
			}

			$html = array_merge( $html, [
								'</ul>',
								'<a href="#" class="poll-ranking-hide button outline">'. \__( 'Return to answers', 'r-enquete' ) .'</a>',
							'</div>',
						'</div>',
						'<template id="poll-graph-item-tmpl">',
							'<li class="poll-graph" data-count="{count}">',
								'<div class="poll-answer">{answer}: ',
									'<span class="votes">{count} '. \__( 'votes', 'r-enquete' ) .'</span> /',
									'<span class="percentual">{percentual}%</span>',
								'</div>',
								'<div class="poll-bar-bg"><div class="poll-bar" style="width: {percentual}%"></div></div>',
							'</li>',
						'</template>',
			] );
			
			$html = implode( "\n", $html );
			$html = apply_filters( 'r/enquete/cpt/sc/output', $html, $args );
			
			return $html;
		}


	}