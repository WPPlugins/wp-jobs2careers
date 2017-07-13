<?php

// load stylesheet if in admin section
function j2c_admin_style() {
	wp_enqueue_style( 'j2c_admin_style', plugins_url( 'css/j2c-admin-style.css', dirname( __FILE__ ) ) );	
}

// load CSS and JS if current page is the display page for jobs
function j2c_enqueue () {

	global $j2c_database_settings_array;
	$j2c_default_location = $j2c_database_settings_array['j2c_default_location'];
	
	if ( get_the_ID() == $j2c_database_settings_array['j2c_display_page'] ) {
		wp_enqueue_style( 'j2c_styling', plugins_url( 'css/j2c-style.css', dirname( __FILE__ ) ) );	
		wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
		wp_enqueue_script( 'j2c_localize_variables', plugins_url( 'js/j2c-scripts.js', dirname( __FILE__ ) ) );	

		wp_localize_script( 'j2c_localize_variables', 'j2c_localized_scripts_var', array(
			'j2c_default_location' => $j2c_default_location, 
		) );
	}
}

?>