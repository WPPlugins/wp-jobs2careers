<?php

include( 'j2c-functions.php' );

function j2c_display_advanced_options ( $j2c_default_location, $j2c_keywords_placeholder, $j2c_location_placeholder, $j2c_page_to_display_jobs, $j2c_permalink_structure ) {
	
	$j2c_select_km = null;
	$j2c_select_job_type = null;
	$j2c_advanced_form = "<div class='j2c-advanced-option-content'>";
	$j2c_advanced_form .= "<h4>Advanced Search</h4>";
	$j2c_advanced_form .= "<form method='get' action=" . get_permalink() . "  name='j2c_advanced_search_form' id='j2c-advanced-search-form-display' />";
	
	$j2c_advanced_form .= "<div class='j2c-advanced-dropdown'>";
	$j2c_advanced_form .= "<div id='j2c-search-title'><span id='j2c-search-title-span'><input type='checkbox' id='j2c-search-title-checkbox' name='title' value='1' />";
	$j2c_advanced_form .= "<label for='j2c-search-title-checkbox'>&nbsp;Search title only</label></span></div>";
	$j2c_advanced_form .= "</div>";
	
	$j2c_advanced_form .= "<div class='j2c-advanced-dropdown'><input type='text' placeholder='" . $j2c_keywords_placeholder . "' name='search' id='j2c_advanced_form_keywords' /></div>";
	
	if ( ! $j2c_permalink_structure ) {
		$j2c_advanced_form .= "<input type='hidden' name='page_id' value=" . get_the_ID(). " />";
	}
	
	if ( ! $j2c_default_location ) {
		$j2c_advanced_form .= "<div class='j2c-advanced-dropdown'><input type='text' placeholder='" . $j2c_location_placeholder . "' name='job_location' id='j2c_advanced_form_location' /></div>";
		
		$j2c_advanced_form .= "<div class='j2c-advanced-dropdown'>";
		$j2c_advanced_form .= "<select name='distance' id='j2c-select-distance'>";	
		$j2c_advanced_form .= "<option value=''  "  . selected( $j2c_select_km, '', false ) . " >Any Distance</option>";
		$j2c_advanced_form .= "<option value='20' "  . selected( $j2c_select_km, '20', false ) . ">Within 20 km</option>";
		$j2c_advanced_form .= "<option value='40' " . selected( $j2c_select_km, '40', false ) . ">Within 40 km</option>";	
		$j2c_advanced_form .= "<option value='80' " . selected( $j2c_select_km, '80', false ) . ">Within 80 km</option>";	
		$j2c_advanced_form .= "</select></div>";
	}	
	
	$j2c_advanced_form .= "<div class='j2c-advanced-dropdown'>";
	$j2c_advanced_form .= "<select name='type' id='j2c-select-job-type'>";
	$j2c_advanced_form .= "<option value='' "  . selected( $j2c_select_job_type, '', false  ) . ">Any Type</option>";		
	$j2c_advanced_form .= "<option value='full-time' "  . selected( $j2c_select_job_type, '1', false  ) . ">Full Time</option>";
	$j2c_advanced_form .= "<option value='part-time' "  . selected( $j2c_select_job_type, '2', false  ) . ">Part Time</option>";
	$j2c_advanced_form .= "<option value='gigs' "  . selected( $j2c_select_job_type, '4', false  ) . ">Gigs</option>";	
	$j2c_advanced_form .= "</select></div>";	
	
	$j2c_advanced_form .= "<div class='j2c-advanced-form-field'><input id='j2c-advanced-submit-form' type='submit' name='findjobs' value='Search' /></div>";
	$j2c_advanced_form .= "</form>";
	$j2c_advanced_form .= "</div>";	
	
	return $j2c_advanced_form;
}

function j2c_job_display ( $content ) {

	global $j2c_database_settings_array; 

	// Initializing variables
	$j2c_keywords = null;
	$j2c_location = null;
	$j2c_select_km = null;
	$j2c_sort_order = null;
	$j2c_set_job_type = null;
	$j2c_keywords_for_display = null;
	$j2c_location_for_display = null;
	$j2c_pagination_display = null;
	$j2c_select_job_type = null;
	$j2c_search_title = null;

	$j2c_publisher_id = $j2c_database_settings_array['j2c_publisher_id'];
	$j2c_publisher_password = $j2c_database_settings_array['j2c_publisher_password'];	
	$j2c_logo_attribution = $j2c_database_settings_array['j2c_logo_attribution'];
	$j2c_page_to_display_jobs = $j2c_database_settings_array['j2c_display_page'];
	$j2c_default_location = $j2c_database_settings_array['j2c_default_location'];
	$j2c_keywords_placeholder = stripslashes( $j2c_database_settings_array['j2c_keywords_placeholder'] );
	$j2c_location_placeholder = stripslashes( $j2c_database_settings_array['j2c_location_placeholder'] );
	$j2c_permalink_structure = j2c_url_structure();
		
	if ( $j2c_page_to_display_jobs !=  get_the_ID() ) { // If not correct page, leave.
		return $content;	
	}
	
	if ( ! $j2c_logo_attribution ) {
		return $content;
	}
	
	if ( isset( $_GET['advanced'] ) ) {
		if ( 'advanced' == strtolower( $_GET['advanced'] ) ) {
			$content = j2c_display_advanced_options( $j2c_default_location, $j2c_keywords_placeholder, $j2c_location_placeholder, $j2c_page_to_display_jobs, $j2c_permalink_structure );
			return $content;
		}
	}
		
	if ( isset( $_GET['type'] ) ) {	
		$j2c_select_job_type = $_GET['type'];
		
		if ( 'full-time' == strtolower( $j2c_select_job_type ) ) {
			$j2c_set_job_type = 1;
		} elseif ( 'part-time' == strtolower( $j2c_select_job_type ) ) {
			$j2c_set_job_type = 2;		
		} elseif ( 'gigs' == strtolower( $j2c_select_job_type ) ) {
			$j2c_set_job_type = 4;
		} else	{
			$j2c_set_job_type = '';
		}	
	}
	
	if ( isset( $_GET['distance'] ) ) {
		if ( '80' == strtolower( $_GET['distance'] ) ) {
			$j2c_select_km = '80';
		} elseif ( '40' == strtolower( $_GET['distance'] ) ) {
			$j2c_select_km = '40';
		} elseif ( '20' == strtolower( $_GET['distance'] ) ) {
			$j2c_select_km = '20';		
		} else {
			$j2c_select_km = '';	
		}
	} 
	
	if ( isset( $_GET['sort'] ) ) {		
		$j2c_sort_order = $_GET['sort'];
	}
		
	$j2c_publisher_id = $j2c_database_settings_array['j2c_publisher_id'];
	$j2c_publisher_password = $j2c_database_settings_array['j2c_publisher_password'];
	
	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {		
		$j2c_ip = $_SERVER['REMOTE_ADDR'];
	}

	if (isset( $_GET['search'] ) ) {
		if (isset( $_GET['search'] ) && ( '' != $_GET['search'] )  ) {
			$j2c_keywords1 = urlencode( ( $_GET['search'] ) );
			$j2c_keywords = urlencode( stripslashes( $_GET['search'] ) );
			$j2c_keywords_for_display = stripslashes( urldecode( $j2c_keywords1 ) );
		}
	}

	if (isset( $_GET['title'] ) ) {
		$j2c_search_title = $_GET['title'];
	}
	
	// Location
	$j2c_geolocation = j2c_geolocation ( $j2c_ip );
	
	if ( $j2c_default_location = $j2c_database_settings_array['j2c_default_location'] ) {
		$j2c_location = urlencode( html_entity_decode( stripslashes( $j2c_default_location ) ) );
	} elseif ( ( ! isset($_GET['job_location']) ) || ( '' == ($_GET['job_location'] ) ) ) {
		$j2c_location = urlencode( stripslashes( $j2c_geolocation ) );
	} else {
		$j2c_location = urlencode( stripslashes( $_GET['job_location'] ) );
	}
		
	$j2c_location1 = $j2c_location;
	$j2c_location_for_display = stripslashes( urldecode( $j2c_location1 ) );
		
	$j2c_page = j2c_get_page_number();

	if ( $j2c_page <= 0 ) {
		$j2c_page = 1;
	}	
	
	$j2c_jobs_per_page = 10;
	$j2c_start = ( $j2c_page * $j2c_jobs_per_page ) - 10;

	$j2c_feedurl = "http://api.jobs2careers.com/api/search.php?id=$j2c_publisher_id&pass=$j2c_publisher_password&ip=$j2c_ip&q=$j2c_keywords&l=$j2c_location&format=xml&title=$j2c_search_title&jobtype=$j2c_set_job_type&d=$j2c_select_km&sort=$j2c_sort_order&start=$j2c_start";
	// Using page wrap to hide the flashing.
	$content .= "<div id='j2c_page_wrap'>";
	$content .= "<div id='j2c-search-form'>";
	$content .= "<form method='get' action=" . get_permalink() . "  name='j2c_search_form' id='j2c-search-form-display' >";
	
	if ( $j2c_permalink_structure ) { // If permalink, use '?', otherwise use '&' for parameters on 'Advanced' link.
		$j2c_query_param = '?';
	} else {
		$content .= "<input type='hidden' name='page_id' value=" . get_the_ID() . " />";
		$j2c_query_param = '&';
	}
	
	$content .= "<div class='j2c-form-field'><input type='text' name='search' placeholder='" . $j2c_keywords_placeholder . "' value='" . $j2c_keywords_for_display . "' id='j2c-keywords' /></div>";
	
	if ( '' == $j2c_database_settings_array['j2c_default_location'] ) {
		$content .= "<div class='j2c-form-field'><input type='text' name='job_location'  placeholder='" . $j2c_location_placeholder . "' value='" . $j2c_location_for_display . "' id='j2c-location' /></div>";
	}
		
	$content .= "<div class='j2c-form-field'><input id='j2c-submit-form' type='submit' name='findjobs' value='Search' /><div><div id='j2c-advanced-link'><a href='" . get_permalink() . $j2c_query_param . "advanced=advanced'>Advanced</a></div></div></div>";
	$content .= "</form>";
	$content .= "</div>";
	$content .= "<div class='j2c-jobs'>";
	
	$j2c_xml = j2c_get_feed_contents( $j2c_feedurl );

	if ( ( ! $j2c_xml ) ) { // If unable to connect to Jobs2Careers, exit
		$content .= "</div></div>";
		return $content;	
	} 
	
	if ( is_ssl() ) {
		$j2c_protocol = 'https';
	} else {
		$j2c_protocol = 'http';
	}	
		

	if ( $j2c_xml->job ) { // If results exist from query

		$content .= "<div class='j2c-numbers-and-sort-div'>";
		
		// Do not display job count if location and keywords are empty
		if( ( $j2c_location_for_display ) || ( $j2c_keywords_for_display  ) || ( $j2c_default_location )  ) {
			$content .= "<span class='j2c-display-numbers'>" . j2c_display_number_of_results( $j2c_xml ) . "</span>";
		}
	
		$j2c_sort_query_string = j2c_sort_query_string ( $j2c_permalink_structure );

		if ( 'd' == strtolower( $j2c_sort_order) ) {
			$content .= "<span class='j2c-sort-links'><span class='j2c-relevance-margin'><a href='" . $j2c_sort_query_string ."&sort=r'><span class='fa fa-list-ul'></span> Relevance</a></span><span class='fa fa-calendar'></span> Date</span>";
		} else {
			$content .= "<span class='j2c-sort-links'><span class='j2c-relevance-margin'><span class='fa fa-list-ul'></span> Relevance</span><a href='" . $j2c_sort_query_string ."&sort=d'><span class='fa fa-calendar'></span> Date</a></span>";
		}
	
		$content .= "</div>";

		foreach( $j2c_xml->job as $j2c_r ) { // Iterate through all jobs
			$j2c_date_posted = date( 'n/d/Y', strtotime( $j2c_r->date ) );
			$content .= "<div class='j2c-listing'>";
			$j2c_onclick = htmlspecialchars( $j2c_r->onclick );
			$content .= "<span class='j2c-jobtitle'><a rel='nofollow' href='#' onclick=" . htmlspecialchars_decode('&quot;') . $j2c_onclick . htmlspecialchars_decode('&quot;') . ">" . $j2c_r->title . "</a></span>";
			$content .= "<p><span class='j2c-company'>" . $j2c_r->company . " - </span><span class='j2c-location'>" . $j2c_r->city . "</span></p>";
			$content .= "<p class='j2c-description'>" . $j2c_r->description . "...</p>";
			$content .= "<p class='j2c-time-posted'><span class='fa fa-calendar'></span> " . $j2c_date_posted . "</p>";
			$content .= "<hr />";
			$content .= "</div>";
		}
		$content .= "<div class='j2c-pagination'>";
		$content .= j2c_pagination( $j2c_xml );	
		$content .= "</div>";		
			
		// Attribution below is required by Jobs2Careers
		$content .= "<br /><div class='j2c-attribution'><div id='j2c-jobs-by-link'><a target='_blank' href='http://www.jobs2careers.com/' rel='nofollow'>Jobs by&nbsp;<a/></div><div id='j2c-jobs-by-image'><a target='_blank' href='http://www.jobs2careers.com/' rel='nofollow'><img class='j2c-attribution-image' src='" . $j2c_protocol . "://assets.j2c.com/img/j2c_publisher_wp_plugin.png' alt='jobs by jobs2careers' title='Jobs2Careers logo' /></a></div></div>";
	} else  {
		$content .= '<h3>No results found</h3>';
	}
	$content .= "<script type='text/javascript' src='" . $j2c_protocol . "://api.Jobs2Careers.com/api/j2c.js'></script>";
	$content .= "</div>";
	$content .= "</div>";

	return $content;
}

?>