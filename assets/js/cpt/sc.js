/***
 *	2019-03-22
 */
jQuery( function( $ ){

	console.log( 'r/enquete/cpt/sc' );

	function tmpl( s ,d ){ for( var p in d ) s = s.replace( new RegExp( '{'+p+'}','g' ), d[ p ] ); return s; };
	
	$.fn.serializeObject = function() {
		var obj = {},
			arr = this.serializeArray()
			;
		arr.forEach( function( item, i ){
			// New
			if( obj[ item.name ] === undefined ){
				obj[ item.name ] = item.value || '';
			}

			// Existing
			else {
				if( !obj[ item.name ].push ){
					obj[ item.name ] = [ obj[ item.name ] ];
				}
				obj[ item.name ].push( item.value || '' );
			}
		});
		return obj;
	};

	$( '.poll-submit' ).on( 'click', function( e ){
		e.preventDefault();
		var $t = $( this ),
			$poll = $t.parents( '.poll' ),
			$title = $poll.find( '.poll-title' ),
			$form = $poll.find( '.poll-form' ),
			$data = $form.serializeObject(),
			$tmpl =  [
					'<div class="alert {type}">',
						'<div class="alert-content">',
							'{msg}',
						'</div>',
						'<a href="#" class="alert-remove">&times;</a>',
					'</div>'
				].join( "\n" )
			;
		
		console.log( $data );
		
		if( !$data.answer ){
			alert( $polls.select_answer );
			return;
		}
		
		$poll.find( '.alert' ).remove();
		
		$poll.addClass( 'sending' );
		
		$.post( $polls.ajax_url, $data, function( data ){
			console.log( data );
			var $alert = tmpl( $tmpl, {
				msg: $polls[ data.status ],
				type: ( data.success ? 'success' : 'error' )
			} );
			console.log( 'alert', $alert );
			$title.after( $alert );
			$poll.removeClass( 'sending' );
			if( data.success ){
				$( '[name="answer"]' ).attr( 'checked', false );
				
				if( data.data ){
					var $graph = $poll.find( '.poll-graphs' ),
						$graph_tmpl = $( '#poll-graph-item-tmpl' ).html(),
						$total = Object.keys( data.data ).reduce( function( total, k ){
							return total + parseInt( data.data[ k ].count );
						}, 0 )
						;
					$graph.empty();
					$.each( data.data, function( i, item ){
						item.percentual = 0;
						if( item.count != 0 ){
							item.percentual = ( ( parseInt( item.count ) / $total ) * 100 ).toFixed( 2 );
						}
						console.log( )
						$graph.append( tmpl( $graph_tmpl, item ) );
					} );
					console.log( { items: data.data, total: $total } );
				}
			}
		}, 'json' );
	} );
	
	$( '.poll-ranking-show' ).on( 'click', function( e ){
		e.preventDefault();
		$( this ).parents( '.poll' ).addClass( 'show-graph' );
	} );
	
	$( '.poll-ranking-hide' ).on( 'click', function( e ){
		e.preventDefault();
		$( this ).parents( '.poll' ).removeClass( 'show-graph' );
	} );
	
	$( document ).on( 'click', '.alert-remove', function( e ){
		e.preventDefault();
		$( this ).parent( '.alert' ).slideUp( function(){
			$( this ).remove();
		} );
	} );
	
});
