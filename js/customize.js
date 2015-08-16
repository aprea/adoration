/**
 * Function for turning a hex color into an RGB string.
 */
function adoration_hex_to_rgb( hex ) {
	var color = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( hex );

	return parseInt( color[1], 16 ) + ", " + parseInt( color[2], 16 ) + ", " + parseInt( color[3], 16 );
}

/**
 * Handles the customizer live preview settings.
 */
( function( $ ) {

	var $style = $( '#adoration-color-scheme-css' ),
		api = wp.customize;

	if ( ! $style.length ) {
		$style = $( 'head' ).append( '<style type="text/css" id="adoration-color-scheme-css" />' ).find( '#adoration-color-scheme-css' );
	}

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-branding a' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-tagline' ).text( to );
		} );
	} );

	// Color Scheme CSS.
	api.bind( 'preview-ready', function() {
		api.preview.bind( 'update-color-scheme-css', function( css ) {
			$style.html( css );
		} );
	} );

} )( jQuery );
