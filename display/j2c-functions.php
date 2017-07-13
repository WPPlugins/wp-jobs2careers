<?php

function j2c_geolocation ( $j2c_remote_address ) {

	$j2c_geo_contents = null;
	$j2c_geo_url = "http://www.geoplugin.net/php.gp?ip=$j2c_remote_address";
	$j2c_geo = curl_init( $j2c_geo_url );
	curl_setopt( $j2c_geo, CURLOPT_URL, $j2c_geo_url );
	curl_setopt( $j2c_geo, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $j2c_geo, CURLOPT_TIMEOUT, 10 );
	$j2c_geolocation_data = curl_exec( $j2c_geo );
	$j2c_geo_header_status = curl_getinfo( $j2c_geo, CURLINFO_HTTP_CODE );
	curl_close( $j2c_geo );
	
	if ( 200 === $j2c_geo_header_status) {
		$j2c_geolocation_data = unserialize($j2c_geolocation_data );
		$j2c_geolocation_county_code = $j2c_geolocation_data['geoplugin_countryCode'];
		$j2c_geolocation_city = $j2c_geolocation_data['geoplugin_city'];
	
		// If within U.S., format is CITY, STATE. If outside the U.S., format is CITY, COUNTRY.
		if ( 'us' == strtolower( $j2c_geolocation_county_code ) ) {
			$j2c_geolocation_region = $j2c_geolocation_data['geoplugin_regionCode'];
			return $j2c_geolocation_city . ', ' . $j2c_geolocation_region; 
		} else {
			return 'Austin, TX';
		}
	
	} else {
		//echo "Geo not available. Header status is " . $j2c_geo_header_status;
		return 'Austin, TX';
	}	

}

function j2c_sort_query_string ( $j2c_permalink_structure ) {

	$j2c_sort_query_string = get_permalink();

	if ( ! $j2c_permalink_structure ) {
		$j2c_sort_query_string .= '&';
	} else {
		$j2c_sort_query_string .= '?';
	} 	
		
	if ( isset( $_GET['search'] ) ) {
		$j2c_sort_query_string .= '&search=' . urlencode( stripslashes( $_GET['search'] ) );;
	}
	
	if ( isset( $_GET['job_location'] ) ) {
		$j2c_sort_query_string .= '&job_location=' . urlencode( stripslashes( $_GET['job_location'] ) );;
	}
	
	if ( isset( $_GET['type'] ) ) {
		$j2c_sort_query_string .= '&type=' . $_GET['type'];		
	}
	
	if ( isset( $_GET['distance'] ) ) {
		$j2c_sort_query_string .= '&distance=' . $_GET['distance'];		
	}
	if ( isset( $_GET['title'] ) ) {
		$j2c_sort_query_string .= '&title=' . $_GET['title'];		
	}

	// Clean up query string
	$j2c_sort_query_string = str_replace('?&', '?', $j2c_sort_query_string);
	$j2c_sort_query_string = str_replace('&&', '&', $j2c_sort_query_string);

	return $j2c_sort_query_string;
}

function j2c_display_number_of_results ( $j2c_xml_page_data ) {
	
	$j2c_display_numbers = null;
	$j2c_si = (int) $j2c_xml_page_data->attributes()->start; // Start Index
	$j2c_firstjob = $j2c_si + 1;// First job # for each page
	$j2c_tv = (int) $j2c_xml_page_data->attributes()->total; // Total # of jobs returned for query
	$j2c_tpages = ceil( $j2c_tv/10 ); // Total # of pages 
	
	$j2c_current_page = j2c_get_page_number();
	
	if ( $j2c_tpages == $j2c_current_page ) {
		$j2c_last_job = $j2c_tv;
	} else {
		$j2c_last_job = $j2c_current_page * 10;
	}
	
	if ( 1  < $j2c_tv) {
		$j2c_display_numbers .= "<strong>" . $j2c_firstjob . " - " . $j2c_last_job . "</strong> of <strong>" . $j2c_tv . "</strong> jobs";
	} elseif ( 1 == $j2c_tv ) {
		$j2c_display_numbers .= "One result";	
	}

	return $j2c_display_numbers;
}

function j2c_get_feed_contents( $j2c_url_to_get_contents ) {

	set_error_handler( 'j2c_error_handling' ); // catch warning 
	$j2c_xml_contents = null;
	$j2c_curl = curl_init();
	curl_setopt( $j2c_curl, CURLOPT_URL, $j2c_url_to_get_contents );
	curl_setopt( $j2c_curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $j2c_curl, CURLOPT_TIMEOUT, 10 );
	$j2c_curl_response = curl_exec( $j2c_curl );
	curl_close( $j2c_curl );
	
	$j2c_bad_format_q_l = 'Bad format, provide q and l';
	$j2c_bad_format = 'Bad format';
	$j2c_bad_ip = 'bad ip';	
	$j2c_bad_location = 'bad location'; // Invalid location
	$j2c_bad_id_pass = 'bad id,pass'; //Invalid Publisher ID/password
	
	if ( strpos( strtolower( $j2c_curl_response ),  $j2c_bad_id_pass ) ){
		_e( 'Invalid Jobs2Careers Publisher ID/Password credentials' );
	} elseif ( strpos( strtolower( $j2c_curl_response ),  $j2c_bad_location ) ) {
		_e( 'Invalid U.S. location specified' );
	} elseif ( strpos( strtolower( $j2c_curl_response ),  $j2c_bad_format_q_l ) ) { // Shouldn't come across this
		_e( 'You must provide either keyword(s) and/or location' );
	} elseif ( strpos( strtolower( $j2c_curl_response ),  $j2c_bad_format ) ) {
		_e( 'Bad Jobs2Careers Publisher ID/Password format or invalid IP address' );
	} elseif ( strpos( strtolower( $j2c_curl_response ),  $j2c_bad_ip ) ) {
		_e( 'Invalid client IP address' );				
	} else {
		try {	
			$j2c_xml_contents = new SimpleXMLElement( $j2c_curl_response ); 
		} catch ( Exception $e ) {
			echo "<b>Exception:</b>  Unable to connect to Jobs2Careers. ", $e->getMessage(), ".\n\n";
		}
		restore_error_handler(); // restore PHPs warnings
	}
	
	return $j2c_xml_contents;
}

function j2c_pagination( $j2c_xml_page_data ) {	

	$j2c_pagination_display = null;
	$j2c_tv = (int) $j2c_xml_page_data->attributes()->total; // Total # of jobs returned for query
	$j2c_tpages = ceil( $j2c_tv/10 ); // Total # of pages 

	$j2c_pagination_display .= paginate_links(
	array(
		'total' => $j2c_tpages,
	) );

	return $j2c_pagination_display;
}

function j2c_get_page_number() {

	if ( get_query_var( 'paged' ) ) {
		$j2c_page_number = ( get_query_var( 'paged' ) );
	} elseif ( get_query_var( 'page' ) ) {
		$j2c_page_number = ( get_query_var( 'page' ) );
	} else {
		$j2c_page_number = 1;
	}	
	return $j2c_page_number;

}

function j2c_url_structure () {

	if ( get_option( 'permalink_structure' ) ) {
		$j2c_permalink_set = true;
	} else {
		$j2c_permalink_set = false;
	}
	
	return $j2c_permalink_set;
	
}

?>