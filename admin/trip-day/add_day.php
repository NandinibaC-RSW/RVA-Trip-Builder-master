<?php

$post_id = $_GET['trip'];
$trip_title = get_the_title($post_id);
$trip_day_starting_address = get_post_meta( $post_id, '_trip_starting_address', true );
$trip_day_starting_lat = get_post_meta( $post_id, '_trip_starting_address_lat', true );
$trip_day_starting_lng = get_post_meta( $post_id, '_trip_starting_address_lang', true );
$day = get_post_meta($post_id, '_trip_day_count', true);
for ($i=1; $i <= $day; $i++) { 
    $current_route = "_route_" . $i;
    $prev_day_end_address = "_route_day_end_address_" .$i;
    $prev_day_end_lat = "_route_day_end_address_lat_" .$i;
    $prev_day_end_lng = "_route_day_end_address_lng_" .$i;
    $current_value = get_post_meta($post_id, $current_route, true);
    if ($current_value) {
        $trip_day_starting_address = get_post_meta($post_id, $prev_day_end_address, true);
        $trip_day_starting_lat = get_post_meta($post_id, $prev_day_end_lat, true);
        $trip_day_starting_lng = get_post_meta($post_id, $prev_day_end_lng, true);
    } else {
        continue;
    }
}


echo '<div class="card">';
echo '<h1>'. $trip_title .'</h1>';
echo '<h3>Trip Stay Editor</h3>';
echo '<form method="POST">';
echo '<input class="full-width" type="text" id="route_title" name="route_title" required placeholder="Title for new stay" />'; 

echo '<h4>Starting Location  : </h4><h5>'. $trip_day_starting_address .'</h5></br>';
echo '<input type="hidden" name="route_start_address" value="'. $trip_day_starting_address .'" />'; 
echo '<input type="hidden" name="route_start_lat" value="'. $trip_day_starting_lat .'" />'; 
echo '<input type="hidden" name="route_start_lng" value="'. $trip_day_starting_lng .'" />'; 
echo '<input type="checkbox" id="final_day_checkbox" name="final_day_checkbox" onchange="toggleDestinationFields()" /> 
      <label for="final_day_checkbox">Final Day</label><br />';

echo '<div id="destination_section">';
    echo '<h3 id="text_hide">Add Destination</h3>
    <p id="text_hide"><b>This is where the traveler will stay. You will be able to add travel stops and additional activities on the next page.</b></p>';

    echo '<label for="trip-category">' . __( 'Category:', 'your-plugin-textdomain' ) . '</label><br/>';
    $args = array(
        'taxonomy' => 'destination_category',
        'name' => 'destination_category',
        'id' => 'destination_categorys',
        'show_option_none' => 'Select category',
        'hide_empty' => false,
        'class' => 'full-width',
    );
    wp_dropdown_categories($args);
    
    echo '<br/><br/>';
    echo '<label for="trip-destination">' . __( 'Destination:', 'your-plugin-textdomain' ) . '</label><br/>';

    $args = array(
        'post_type' => 'destination',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );


    // Retrieve destinations
    $selected_destination_name = "_selected_destinations_" . $day;
    $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);
    $all_destinations = get_posts($args);
    echo '<div class="destinations"><select name="destination[]" id="destination" class="dest-width add-ending-dests" required>';
    echo " <option disabled selected value=''>Select</option>";

    foreach ($all_destinations as $single_destination) {

        $option_value = $single_destination->ID;
        $destination_address = get_post_meta( $option_value, '_destination_address', true);
        $destination_lat =  get_post_meta( $option_value, '_destination_lat', true );
        $destination_lang =  get_post_meta( $option_value, '_destination_lang', true );

        // Output the destination details as data attributes
        echo '<option value="' . esc_attr($option_value) . '"  data-address="' . esc_attr($destination_address) . '" data-lat="' . esc_attr($destination_lat) . '" data-lng="' . esc_attr($destination_lang) . '">' . esc_html($single_destination->post_title) . '</option>';
    }

    echo '</select></div>';    
    echo '<br> <a class="button add-dest">Add New</a>';
    echo '</div>';
    
    wp_reset_postdata();

    // echo '<br><br><label for="trip_ending_address">' . __( 'Ending Address : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input class="full-width" type="hidden" id="trip_ending_address" name="day_end_address" /> 
          <input type="hidden" id="dest_address_id" name="day_end_address_dest_id">
          <input type="hidden" id="ending_address_lat" name="day_end_address_lat">
          <input type="hidden" id="ending_address_lang" name="day_end_address_lng"><br />';
    echo '</br></br><button type="submit" class="preview button" name="plan_route">Start Building</button></form>';
    echo '</br></br>';

    echo '<div class="add-dest-modal">
            <div class="dest-modal-content">
            <div class="modal-heading">
                <b>Add New Destination </b>
                <span class="closes" >&times;</span>
            </div><hr>';
    echo '<form id="destination-form" method="post">';
    echo '<label for="destination-title">' . __( 'Name of Stop :', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="destination-title" name="destination_title" class="full-width" required/><br />';

    echo '<br/><label for="trip-category">' . __( 'Category:', 'your-plugin-textdomain' ) . '</label><br/>';
        
    $args = array(
        'taxonomy' => 'destination_category',
        'name' => 'destination_category',
        'id' => 'destination_category',
        'required' => 'required',
        'show_option_none' => 'Select a Destination category',
        'hide_empty' => false,
    );
    wp_dropdown_categories($args);

    echo '<br/><br/><label for="destination-address">' . __( 'Address:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="dest_address" name="destination_address" class="full-width" />
          <input type="hidden" id="dest_address_lat" name="destination_lat">
          <input type="hidden" id="dest_address_lng" name="destination_lang">  <br /><br/>';


    echo '<label for="destination-checkin">' . __( 'Check-in Hours & Minutes:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="time" id="destination-checkin" name="check_in_time" class="full-width" /><br /><br/>';

    echo '<label for="destination-phone">' . __( 'Phone:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="number" id="destination-phone" name="dest_phone" class="full-width" /><br /><br/>';

    echo '<label for="destination-website">' . __( 'Website:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="url" id="destination-website" name="dest_website" class="full-width" /><br /><br/>';

    echo '<label for="destination-description">' . __( 'Short Description:', 'your-plugin-textdomain' ) . '</label>';
    echo '<textarea id="destination-description" name="destination_description" class="full-width"></textarea><br />';
    
    echo '<label for="destination-image">' . __( 'Image:', 'your-plugin-textdomain' ) . '</label>';
    echo '<br/><br/><div id="image-preview">';
    echo '</div>';
    echo '<input type="hidden" id="destination-image" name="destination_image" class="full-width" />
        <input type="button" id="upload-image-button" class="button" value="Upload Image" />';

    echo '<hr><br/><input type="submit" class="preview button" value="Save Destination"><br><br/>';
    echo '</form> </div> </div> ';

        // <hr>  render_document_upload_meta_box($post_id);
    echo '</div>';


if(isset($_POST['plan_route'])){
    $counter = get_post_meta( $post_id, '_trip_day_count', true );
    $day_start_address = $_POST['route_start_address'];
    $day_start_address_lat = $_POST['route_start_lat'];
    $day_start_address_lng = $_POST['route_start_lng']; 
    $day_end_address_lat = $_POST['day_end_address_lat'];
    $day_end_address_lng = $_POST['day_end_address_lng'];       
    // $selected_destination = $_POST['destination'];       
    
    if (isset($_POST['final_day_checkbox']) && $_POST['final_day_checkbox'] == 'on') {
        // If it's the final day, use the ending address as the destination
        $selected_destination = array(
            'address' => get_post_meta( $post_id, '_trip_ending_address', true ),
            'lat' => get_post_meta( $post_id, '_trip_ending_address_lat', true ),
            'lng' => get_post_meta( $post_id, '_trip_ending_address_lang', true )
        );
    } else {
        // If it's not the final day, use the selected destination
        $selected_destination = $_POST['destination'];
    }
    $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$day_start_address_lat,$day_start_address_lng&destination=$day_end_address_lat,$day_end_address_lng&units=imperial&key=".esc_attr(get_option('google_api_key'));
    $directions_data = wp_remote_get($url);
    $response_body = wp_remote_retrieve_body($directions_data);
    $data = json_decode($response_body);

    if ($data && isset($data->routes[0]->legs)) {
        $route_data = $data->routes[0]->legs;
     }

    // $distance = $data->routes[0]->legs[0]->distance->text;
    $duration_seconds = $data->routes[0]->legs[0]->duration->value;
    $hours = floor($duration_seconds / 3600);
    $minutes = floor(($duration_seconds % 3600) / 60);
    $end_travel_time = sprintf('%d:%02d', $hours, $minutes);
        
        $next_route = "_route_" . ($counter + 1);
        // $next_mile = "_route_miles_" . ($counter + 1);
        // $next_time = "_route_time_" . ($counter + 1);
        $next_title = "_route_title_" . ($counter + 1);
        $next_destination_category = "_route_destination_category_" . ($counter + 1);
        $next_destination = "_route_destination_" . ($counter + 1);
        $next_day_start_address = "_route_day_start_address_" . ($counter + 1);
        $next_route_day_start_address_lat = "_route_day_start_address_lat_" . ($counter + 1);
        $next_route_day_start_address_lang = "_route_day_start_address_lng_" . ($counter + 1);        
        $next_day_end_address = "_route_day_end_address_" . ($counter + 1);
        $next_day_end_address_dest_id = "_route_day_end_address_dest_id_" . ($counter + 1);
        $next_route_day_end_address_lat = "_route_day_end_address_lat_" . ($counter + 1);
        $next_route_day_end_address_lang = "_route_day_end_address_lng_" . ($counter + 1);
        $selected_destination_name = "_selected_destinations_" .($counter + 1);
        $day_description_name = "_day_description_" .($counter + 1);
        $time_name = "_end_route_time_" .($counter + 1);
        $end_route_name = "_end_route_" .($counter + 1);



            update_post_meta($post_id, $next_day_start_address, $day_start_address);
            update_post_meta($post_id, $next_route_day_start_address_lat, $day_start_address_lat);
            update_post_meta($post_id, $next_route_day_start_address_lang, $day_start_address_lng);
            update_post_meta($post_id, $next_route, $response_body);
            // update_post_meta($post_id, $next_mile, $distance);
            // update_post_meta($post_id, $next_time, $duration);
            update_post_meta($post_id, $next_title, sanitize_text_field( $_POST['route_title'] ));
            update_post_meta($post_id, $next_destination_category, sanitize_text_field( $_POST['destination_category'] ));
            update_post_meta($post_id, $next_destination, sanitize_text_field( $selected_destination ));
            update_post_meta($post_id, $next_day_end_address, sanitize_text_field( $_POST['day_end_address'] ));
            update_post_meta($post_id, $next_day_end_address_dest_id, sanitize_text_field( $_POST['day_end_address_dest_id'] ));
            update_post_meta($post_id, $next_route_day_end_address_lat, sanitize_text_field( $_POST['day_end_address_lat'] ));
            update_post_meta($post_id, $next_route_day_end_address_lang, sanitize_text_field( $_POST['day_end_address_lng'] ));      
            update_post_meta($post_id, '_trip_day_count', $counter+1);   
            update_post_meta($post_id, $selected_destination_name, array());     
            update_post_meta($post_id, $day_description_name, ""); 
            update_post_meta($post_id, $time_name, $end_travel_time);
            update_post_meta($post_id, $end_route_name, $route_data);
            
            // update_post_meta($post_id, '_upload_document', $_POST['upload_document']);        
            $counters = $counter+1;
        echo '<script>window.location.href = "' . admin_url( 'admin.php?page=trip-day&tab=edit_day&trip=' . $post_id .'&day=' . $counters ) . '";</script>';
       
}           