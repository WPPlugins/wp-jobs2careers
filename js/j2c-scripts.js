
// eliminate flashing of search form
document.write( '<style type="text/css">#j2c_page_wrap{display:none}</style>' );
document.write( '<style type="text/css">.j2c-advanced-option-content{display:none}</style>' );
jQuery( function( $ ) {
	$( '#j2c_page_wrap' ).css( 'display','block' );
	$( '.j2c-advanced-option-content' ).css( 'display','block' );
});

jQuery( document ).ready( function( $ ) {
	
	var j2c_is_location_field_showing;
	
	j2c_is_location_field_showing = j2c_localized_scripts_var.j2c_default_location;

	jQuery.fn.j2c_calculate_form_display = function() {
		
		var j2c_search_form, 
			j2c_min_inline_field_length, 
			j2c_div_size_collapse, 
			j2c_numbers_and_sort_div,
			j2c_advanced_option_content,
			j2c_max_block_field_length;
		
		j2c_search_form = $( '#j2c-search-form' ).outerWidth();	
		j2c_numbers_and_sort_div = $( '.j2c-jobs' ).outerWidth();
		j2c_advanced_option_content = $( '.j2c-advanced-option-content' ).outerWidth();
		j2c_advanced_field_inputs_width = .9 * j2c_advanced_option_content;
		
		// Advanced field widths
		$( '#j2c-select-job-type' ).css( 'width', j2c_advanced_field_inputs_width );
		$( '#j2c-select-distance' ).css( 'width', j2c_advanced_field_inputs_width );		
		$( '#j2c_advanced_form_keywords' ).css( 'width', j2c_advanced_field_inputs_width );
		$( '#j2c_advanced_form_location' ).css( 'width', j2c_advanced_field_inputs_width );
		$( '#j2c-search-title' ).css( 'width', j2c_advanced_field_inputs_width );
		
		// If default location is not set
		if 	( '' == j2c_is_location_field_showing ) {	
			j2c_min_inline_field_length = Math.ceil( .35 * j2c_search_form );
			j2c_div_size_collapse = 500;
		} else {
			j2c_min_inline_field_length = Math.ceil( .5 * j2c_search_form );
			j2c_div_size_collapse = 400;
		}
		
		j2c_max_block_field_length = ( .6 * j2c_div_size_collapse );
		
		// If fields are inline (prior to collapsing)
		if ( j2c_search_form >= j2c_div_size_collapse ) {
			$( '.j2c-form-field' ).css( 'display', 'inline-block' );
			$( '.j2c-form-field' ).css( 'margin-right', '2px' );
			$( '.j2c-form-field' ).css( 'margin-left', '2px' );			
			$( '#j2c-keywords' ).css( 'width', j2c_min_inline_field_length );
			$( '#j2c-location' ).css( 'width', j2c_min_inline_field_length );	
			$( '#j2c-submit-form' ).css( 'width', 'initial' );	
			$( '#j2c-submit-form' ).css( 'padding-right', '15px' );
			$( '#j2c-submit-form' ).css( 'padding-left', '15px' );
			$( '#j2c-submit-form' ).css( 'vertical-align', 'top' );				
			$( '.j2c-form-field' ).css( 'vertical-align', 'top' );	
			$( '#j2c-keywords' ).css( 'max-width', '400px' );
			$( '#j2c-location' ).css( 'max-width', '400px' );		
			$( '.j2c-numbers-and-sort-div' ).css( 'max-width', '700px' );
			$( '.j2c-numbers-and-sort-div' ).css( 'width', j2c_numbers_and_sort_div );								
		} else {			
			$( '.j2c-form-field' ).css( 'display', 'block' );				
			$( '.j2c-form-field' ).css( 'margin-top', '5px' );
			$( '.j2c-form-field' ).css( 'margin-bottom', '5px' );				
			$( '#j2c-keywords' ).css( 'max-width', ( .9 * j2c_numbers_and_sort_div) );
			$( '#j2c-location' ).css( 'max-width', ( .9 * j2c_numbers_and_sort_div) );
			$( '#j2c-keywords' ).css( 'width', ( .9 * j2c_numbers_and_sort_div) );
			$( '#j2c-location' ).css( 'width', ( .9 * j2c_numbers_and_sort_div) );
			$( '#j2c-submit-form' ).css( 'width', ( .9 * j2c_numbers_and_sort_div) );
			$( '.j2c-numbers-and-sort-div' ).css( 'width', j2c_numbers_and_sort_div );
		} 
	};
	
	// Calculate form size upon resizing
	jQuery( window ).resize( function( $ ) {
		jQuery.fn.j2c_calculate_form_display();
	});
	
	// Calculate form size upon page load
	jQuery.fn.j2c_calculate_form_display();
	
});	