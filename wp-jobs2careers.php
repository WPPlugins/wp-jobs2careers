<?php
/*
Plugin Name: WP Jobs2Careers
Plugin URI: https://www.skipthedrive.com/wp-jobs2careers-plugin/
Description: Connect to the Jobs2Careers API and display jobs on your site.
Version: 1.2
Author: Pete Metz
Author URI: https://www.skipthedrive.com
License: GPL2
License URL: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No scripts please' );

/******************
* Globals and constants
*******************/

define( 'j2c_no_pages_error', 'You have no pages created. You must create a page to display your jobs on.' );
define( 'j2c_no_page_selected', 'You must select a page to display jobs on.' );
define( 'j2c_no_api_key', 'You must enter a valid Jobs2Careers Publisher ID.' );
define( 'j2c_no_publisher_password', 'You must enter your Jobs2Careers Publisher Password.' );
define( 'j2c_logo_attribution_declined', 'You must agree to display Jobs2Careers attribution on your jobs page.' );
define( 'j2c_default_jobs_page', '--- Select Page ---' );

$j2c_list_of_pages = get_pages(); // Get list of all pubished pages
$j2c_database_settings_array = get_option( 'j2c_admin_options' ); // All plugin DB settings

/******************
* Files to include
*******************/

include( 'admin/j2c-admin.php' );
include( 'display/j2c-job-display.php' );
include( 'load-control/j2c-enqueue.php' );
include( 'error-handling/j2c-error-handling.php' );

/******************
* Hooks
*******************/

add_action( 'admin_menu', 'j2c_settings_menu' ); // Create admin settings and form
add_filter( 'the_content', 'j2c_job_display' ); // Display jobs
add_action( 'wp_enqueue_scripts', 'j2c_enqueue', 9999 ); // High priority to ensure loading after theme
add_action( 'admin_enqueue_scripts', 'j2c_admin_style' ); //Style for admin section

/******************
* Initialize DB
*******************/

register_activation_hook( __FILE__, 'j2c_set_default_db_values' );

function j2c_set_default_db_values () {

	global $j2c_database_settings_array;
	
	if ( ! $j2c_database_settings_array ) { // If the option isn't present, set defaults
		$j2c_db_array = array(
			'j2c_publisher_id' => '',
			'j2c_publisher_password' => '',	
			'j2c_logo_attribution' => false,
			'j2c_display_page' => j2c_default_jobs_page,
			'j2c_keywords_placeholder' => 'Enter keyword(s)',
			'j2c_default_location' => '',
			'j2c_location_placeholder' => 'Enter location',
		);
		add_option( 'j2c_admin_options', $j2c_db_array );
	}
}

?>