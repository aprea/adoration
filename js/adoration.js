( function( $ ) {

	$( 'time.entry-published' ).on( "click", function() {
		var old_date = $(this).html().trim();
		$(this).html( $(this).attr( 'data-display-toggle' ) );
		$(this).attr( 'data-display-toggle', old_date );
	} );

} )( jQuery );
