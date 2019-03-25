jQuery( function( $ ){

	console.log( 'r/enquete/cpt/metabox' );

	function tmpl( s ,d ){ for( var p in d ) s = s.replace( new RegExp( '{'+p+'}','g' ), d[ p ] ); return s; };
	
	// https://gist.github.com/gordonbrander/2230317
	function ID(){
		// Math.random should be unique because of its seeding algorithm.
		// Convert it to base 36 (numbers + letters), and grab the first 9 characters
		// after the decimal.
		return ''+ Math.random().toString(36).substr( 2, 9 );
	};

	$( '#polls-add-answer' ).on( 'click', function( e ){
		e.preventDefault();
		
		var $target = $( '#polls-options tbody' ),
			$tmpl = $( '#poll-item-tmpl' ).html(),
			$id = ID(),
			$new = tmpl( $tmpl, { id: $id } );
		$target.append( $new );
	} );
	
	$( document ).on( 'click', '.poll-item-remove', function( e ){
		e.preventDefault();
		
		var $t = $( this ),
			$id = $t.data( 'id' ),
			$target = $( '#poll-item-'+ $id )
			;
		if( $target.length ){
			$target.remove();
		}
		else {
			console.log( $id +' not found' );
		}
	} );
	
});
