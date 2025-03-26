<?php

$post_id = $_GET['trip'];
    
        if(isset($_GET['day_delete'])){
        
        $day_delete = $_GET['day_delete'];
        $del_route = "_route_" .$day_delete;
        $del_mile = "_route_miles_" .$day_delete;
        $del_time = "_route_time_" .$day_delete;
        $del_title = "_route_title_" .$day_delete;
        $del_destination_category = "_route_destination_category_" .$day_delete;
        $del_destination = "_route_destination_" .$day_delete;
        $del_day_start_address = "_route_day_start_address_" .$day_delete;
        $del_route_day_start_address_lat = "_route_day_start_address_lat_" .$day_delete;
        $del_route_day_start_address_lang = "_route_day_start_address_lng_" .$day_delete;        
        $del_day_end_address = "_route_day_end_address_" .$day_delete;
        $del_route_day_end_address_lat = "_route_day_end_address_lat_" .$day_delete;
        $del_route_day_end_address_lang = "_route_day_end_address_lng_" .$day_delete;

        delete_post_meta($post_id, $del_route);
        delete_post_meta($post_id, $del_mile);
        delete_post_meta($post_id, $del_time);
        delete_post_meta($post_id, $del_title);
        delete_post_meta($post_id, $del_destination_category);
        delete_post_meta($post_id, $del_destination);
        delete_post_meta($post_id, $del_day_start_address);
        delete_post_meta($post_id, $del_route_day_start_address_lat);
        delete_post_meta($post_id, $del_route_day_start_address_lang);
        delete_post_meta($post_id, $del_day_end_address);
        delete_post_meta($post_id, $del_route_day_end_address_lat);
        delete_post_meta($post_id, $del_route_day_end_address_lang);  

        echo '<script>window.location.href = "' . admin_url( 'post.php?post=' . $post_id .'&action=edit') . '";</script>';
    }
    
    $day = $_GET['day'];

    $trip_title = get_the_title($post_id);
    $current_route = "_route_" . $day;
    $prev_day_end_address = "_route_day_start_address_" .$day;
    $prev_day_end_lat = "_route_day_start_address_lat_" .$day;
    $prev_day_end_lng = "_route_day_start_address_lng_" .$day;
    $route_title_name = "_route_title_" .$day;
    $destination_category_name = "_route_destination_category_" .$day;
    $destination_id_name = "_route_day_end_address_dest_id_" .$day;
    $route_day_end_address_name = "_route_day_end_address_" .$day;
    $route_day_end_address_lat_name = "_route_day_end_address_lat_" .$day;
    $route_day_end_address_lng_name = "_route_day_end_address_lng_" .$day;
    $destination_name = "_route_destination_" .$day;
    $current_value = get_post_meta($post_id, $current_route, true);
    $time_name = "_end_route_time_" .$day;
    $day_description_name = "_day_description_" .$day;
    
    if ($current_value) {
        $trip_day_starting_address = get_post_meta($post_id, $prev_day_end_address, true);
        $trip_day_starting_lat = get_post_meta($post_id, $prev_day_end_lat, true);
        $trip_day_starting_lng = get_post_meta($post_id, $prev_day_end_lng, true);
        $destination_category = get_post_meta($post_id, $destination_category_name, true);
        $destination = get_post_meta($post_id, $destination_name, true);
        $route_day_end_dest_id = get_post_meta($post_id, $destination_id_name, true);
        $route_day_end_address = get_post_meta($post_id, $route_day_end_address_name, true);
        $route_day_end_address_lat = get_post_meta($post_id, $route_day_end_address_lat_name, true);
        $route_day_end_address_lng = get_post_meta($post_id, $route_day_end_address_lng_name, true);
        $route_title = get_post_meta($post_id, $route_title_name, true);
        $day_description = get_post_meta($post_id, $day_description_name, true);
        $route_time = get_post_meta($post_id, $time_name, true);
        preg_match_all('/\d+/', $route_time, $matches);
        $integers = array_map('intval', $matches[0]);
        
        $end_travel_time_hour = $integers[0] ?? 0;
        $end_travel_time_minute = $integers[1] ?? 0;
        if (count($integers) == 0) {
            $end_travel_time_hour = $end_travel_time_minute = 0;
        } elseif (count($integers) == 1) {
            $end_travel_time_minute = 0;
        }
        if ($route_day_end_address) {
            $route_day_end_address = $route_day_end_address;
            $route_day_end_address_lat = $route_day_end_address_lat;
            $route_day_end_address_lng = $route_day_end_address_lng;
        } else {
            $route_day_end_address = get_post_meta($post_id, '_trip_ending_address', true);
            $route_day_end_address_lat = get_post_meta($post_id, '_trip_ending_address_lat', true);
            $route_day_end_address_lng = get_post_meta($post_id, '_trip_ending_address_lang', true);
        }
    } else {
        echo '<script>window.location.href = "' . admin_url( 'admin.php?page=trip-day&tab=day_overview&trip=' . $post_id ) . '";</script>';
    }
    
    echo '<div style ="width:90%">';
    echo '<form method="POST">';
    echo '<input type="hidden" id="filter_nonce" value="'.wp_create_nonce('filter-destinations').'">';
    echo '<input type="submit" name="route_edit" class="button button-primary button-large preview" value="Update">';
    echo '<a class="button button-de button-large preview delete-button" data-post-id="' . $post_id . '" data-day="' . $day . '">Delete</a>';

    echo '<div id="deleteConfirmationModal">
        <div>
            <p>Are you sure you want to remove this stay from the trip?</p>
            <a class="button" id="cancelButton">Cancel</a>
            <a class="button button-primary preview" id="deleteButton">Delete</a>
        </div>
      </div>';

    echo '<h1>'. $trip_title .'</h1>';
    echo '<h3>Trip Stay Editor</h3>';
    echo '<label for="route_title">' . __( 'Title : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input class="full-width" type="text" id="route_title" name="route_title" value="'. $route_title .'" placeholder="Title" /></br></br>';
    echo '<label for="day_description">' . __( 'Description : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<textarea id="day_description" class="full-width" name="day_description" placeholder="Description is seen on the customer&#39;s page" rows="3">'. $day_description .'</textarea></br></br>';
    function render_rv_fields($rv_number, $day, $post_id) {
    // Retrieve saved values if they exist
    $route_reservation_name = "_route_reservation_" . $rv_number . "_" . $day;
    $route_site_desc_name = "_route_site_desc_" . $rv_number . "_" . $day;
    $route_nightly_rate_name = "_route_nightly_rate_" . $rv_number . "_" . $day;
    $route_special_notes_name = "_route_special_notes_" . $rv_number . "_" . $day;
    $route_deposit_name = "_route_deposit_" . $rv_number . "_" . $day;
    $route_balance_due_name = "_route_balance_due_" . $rv_number . "_" . $day; // New field

    $route_reservation = get_post_meta($post_id, $route_reservation_name, true);
    $route_site_desc = get_post_meta($post_id, $route_site_desc_name, true);
    $route_nightly_rate = get_post_meta($post_id, $route_nightly_rate_name, true);
    $route_deposit = get_post_meta($post_id, $route_deposit_name, true);
    $route_balance_due = get_post_meta($post_id, $route_balance_due_name, true); // New field
    $route_special_notes = get_post_meta($post_id, $route_special_notes_name, true);

    echo '<div class="rv-box">';
    echo '<h2>RV ' . $rv_number . '</h2>';
    echo '<label for="route_reservation_' . $rv_number . '">' . __('Reservation : ', 'your-plugin-textdomain') . '</label></br>';
    echo '<input class="half-width" type="text" id="route_reservation_' . $rv_number . '" name="route_reservation_' . $rv_number . '" value="' . esc_attr($route_reservation) . '" /></br>';
    echo '<label for="route_site_desc_' . $rv_number . '">' . __('Site Description : ', 'your-plugin-textdomain') . '</label></br>';
    echo '<input class="half-width" type="text" id="route_site_desc_' . $rv_number . '" name="route_site_desc_' . $rv_number . '" value="' . esc_attr($route_site_desc) . '" /></br>';
    echo '<label for="route_nightly_rate_' . $rv_number . '">' . __('Nightly Rate : ', 'your-plugin-textdomain') . '</label></br>';
    echo '<div class="input-currency-wrapper">';
    echo '<span class="currency-symbol">$</span>';
    echo '<input class="half-width currency-input" type="text" id="route_nightly_rate_' . $rv_number . '" name="route_nightly_rate_' . $rv_number . '" value="' . esc_attr($route_nightly_rate) . '" /></br>';
    echo '</div>';
    echo '<label for="route_deposit_' . $rv_number . '">' . __('Deposit : ', 'your-plugin-textdomain') . '</label></br>';
    echo '<div class="input-currency-wrapper">';
    echo '<span class="currency-symbol">$</span>';
    echo '<input class="half-width currency-input" type="text" id="route_deposit_' . $rv_number . '" name="route_deposit_' . $rv_number . '" value="' . esc_attr($route_deposit) . '" /></br>';
    echo '</div>';
    echo '<label for="route_balance_due_' . $rv_number . '">' . __('Balance Due : ', 'your-plugin-textdomain') . '</label></br>';
    echo '<div class="input-currency-wrapper">';
    echo '<span class="currency-symbol">$</span>';
    echo '<input class="half-width currency-input" type="text" id="route_balance_due_' . $rv_number . '" name="route_balance_due_' . $rv_number . '" value="' . esc_attr($route_balance_due) . '" /></br>';
    echo '</div>';
    echo '<label for="route_special_notes_' . $rv_number . '">' . __('Special Notes : ', 'your-plugin-textdomain') . '</label></br>';
    echo '<input class="half-width" type="text" id="route_special_notes_' . $rv_number . '" name="route_special_notes_' . $rv_number . '" value="' . esc_attr($route_special_notes) . '" /></br>';
    echo '</div>';
}

echo '<div class="rv-container">';
for ($i = 1; $i <= 7; $i++) {
    render_rv_fields($i, $day, $post_id);
}
echo '</div>';

    echo '<h3>' . __( 'Starting Location', 'your-plugin-textdomain' ) . '</h3>';
    echo '<h4>'. $trip_day_starting_address .'</h4></br>';
    echo '<input class="full-width" type="hidden" id="trip_starting_address" name="day_start_address"  value="'. $trip_day_starting_address .'"/>';
    echo '<input type="hidden" name="route_start_address" value="'. $trip_day_starting_address .'" />'; 
    echo '<input type="hidden" name="route_start_lat" id="starting_address_lat" value="'. $trip_day_starting_lat .'" />'; 
    echo '<input type="hidden" name="route_start_lng" id="starting_address_lang" value="'. $trip_day_starting_lng .'" />'; 
    echo '<input type="hidden" name="day" id="day_count" value="'. $day .'" />'; 
    
    echo '<h3>Stops <a class="button stops">+ Add</a></h3>';
    echo '<div class="stops-div">';
    echo '<label for="trip-category">' . __( 'Category:', 'your-plugin-textdomain' ) . '</label><br/>';
    $args = array(
        'taxonomy' => 'destination_category',
        'name' => 'destination_category',
        'id' => 'destination_categorys',
        'show_option_none' => 'Select category',
        'hide_empty' => false,
        'class' => 'full-width',
        'selected' => $destination_category,
    );
    wp_dropdown_categories($args);
    
    echo '<br/><br/>';
    echo '<label for="trip-destination">' . __( 'Stops: ', 'your-plugin-textdomain' ) . '</label><br/>';

    $args = array(
        'post_type' => 'destination',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );


    // Retrieve selected destinations for the specific day
    $selected_destination_name = "_selected_destinations_" . $day;
    $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);
    $all_destinations = get_posts($args);
    echo '<input type="hidden" name="selected_values" id="selected-values-input" value="" />';
    // Replace your current destination select box code with:
    echo '<div class="destinations">';
    echo '<select name="destination[]" id="destination" class="dest-width select2-destinations" multiple>';

    foreach ($all_destinations as $single_destination) {
        $option_value = $single_destination->ID;
        $selected_attribute = '';

        if (is_array($selected_destinations) && array_key_exists($option_value, $selected_destinations)) {
            $selected_attribute = 'selected';
            $destination_details = $selected_destinations[$option_value];
            $destination_address = $destination_details['address'];
            $destination_lat = $destination_details['lat'];
            $destination_lng = $destination_details['lng'];

            echo '<option value="' . esc_attr($option_value) . '" ' . $selected_attribute . ' data-address="' . esc_attr($destination_address) . '" data-lat="' . esc_attr($destination_lat) . '" data-lng="' . esc_attr($destination_lng) . '">' . esc_html($single_destination->post_title) . '</option>';
        } else {
            echo '<option value="' . esc_attr($option_value) . '" ' . $selected_attribute . '>' . esc_html($single_destination->post_title) . '</option>';
        }
    }

    echo '</select></div>';
    // echo '<br> <a class="button" href="post-new.php?post_type=destination">Add New</a>';
    echo '<br> <a class="button add-dest">Add New</a></div>';
    echo '<ul id="selected-options-container" class="selected-options-table"></ul>';
    echo '<script>
            var selectedDestinations = ' . json_encode(array_values($selected_destinations)) . ';
          </script>';
    wp_reset_postdata();
    $lastNode = end($selected_destinations);
    
    $route_start_address = $trip_day_starting_address;
    $route_start_address = isset($lastNode['address']) ? $lastNode['address'] : $trip_day_starting_address;

    // echo '<br/>';
    // $route_start_address = echo "<script>getLastDestinationAddress()</script>";
    echo '<h3>' . __( 'Destination', 'your-plugin-textdomain' ) . '</h3>';
    echo '<input class="dest-width" type="text" id="trip_ending_address" name="day_end_address"  value="'. $route_day_end_address .'"/> 
          <input type="hidden" id="ending_address_lat" name="day_end_address_lat" value="'. $route_day_end_address_lat .'">
          <input type="hidden" id="ending_address_lang" name="day_end_address_lng" value="'. $route_day_end_address_lng .'">';
    echo '<a class="preview button display-map" onclick="open_map(`'. $route_day_end_dest_id .'`,`'. $route_start_address .'`,`'. $route_day_end_address .'`)" >Edit Route</a><br />';

    echo '<br/>';
        echo '<div id="container" class="map-containers" style="display: none;"><div class="map" id="map'. $route_day_end_dest_id .'"></div><div id="sidebar"><div id="panel'. $route_day_end_dest_id .'"></div></div></div>
        ';
    // echo '<label for="travel_time_hour">' . __( 'Travel Time : ', 'your-plugin-textdomain' ) . '</label><br/>';
    echo '<div class="estimate-time"><input type="number" class="small-width" id="travel_time_hour" name="end_travel_time_hour" placeholder="Hours" value="'. $end_travel_time_hour .'"/> 
          <input type="number" class="small-width" id="travel_time_minute" name="end_travel_time_minute" placeholder="Minutes" value="'. $end_travel_time_minute .'"></div>';
    
    echo '<br/><br/><h3>Activities  <a class="button activities">+ Add</a></h3>';
    echo '<div class="activities-div">';
    echo '<label for="trip-category">' . __( 'Category:', 'your-plugin-textdomain' ) . '</label><br/>';
    $args = array(
        'taxonomy' => 'destination_category',
        // 'name' => 'destination_category',
        'id' => 'destination_category_activity',
        'show_option_none' => 'Select category',
        'hide_empty' => false,
        'class' => 'full-width',
        'selected' => $destination_category,
    );
    wp_dropdown_categories($args);
    
    echo '<br/><br/>';
    echo '<label for="trip-destination">' . __( 'Stops: ', 'your-plugin-textdomain' ) . '</label><br/>';

    $args = array(
        'post_type' => 'destination',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );


    // Retrieve selected activity for the specific day
    $selected_activity_name = "_selected_activity_" . $day;
    $selected_activity = get_post_meta($post_id, $selected_activity_name, true) ?: array();

    $all_destinations = get_posts($args);
    echo '<input type="hidden" name="selected_activity_values" id="selected-values-activity" value="" />';
    echo '<div class="destinations"><select name="destination[]" id="destination_act" class="dest-width" multiple>';
    // echo '<option value="">Select destinations</option>';

    foreach ($all_destinations as $single_destination) {
        $option_value = $single_destination->ID;
        $selected_attribute = '';

        // Check if the destination ID is in the selected destinations array
        if (is_array($selected_activity) && array_key_exists($option_value, $selected_activity)) {
            $selected_attribute = 'selected';
            $destination_details = $selected_activity[$option_value];
            $destination_address = $destination_details['address'];
            $destination_lat = $destination_details['lat'];
            $destination_lng = $destination_details['lng'];

            // Output the destination details as data attributes
            echo '<option value="' . esc_attr($option_value) . '" ' . $selected_attribute . ' data-address="' . esc_attr($destination_address) . '" data-lat="' . esc_attr($destination_lat) . '" data-lng="' . esc_attr($destination_lng) . '">' . esc_html($single_destination->post_title) . '</option>';
        } else {
            echo '<option value="' . esc_attr($option_value) . '" ' . $selected_attribute . '>' . esc_html($single_destination->post_title) . '</option>';
        }
    }

    echo '</select></div>';    
    // echo '<br> <a class="button" href="post-new.php?post_type=destination">Add New</a>';
    echo '<br> <a class="button add-dest">Add New</a></div>';
    echo '<ul id="selected-act-options-container" class="selected-options-table"></ul>';
    echo '<script>
            var selectedActivities = ' . json_encode(array_values($selected_activity)) . ';
             update_dest('. $destination_category .');
             update_dest_act('. $destination_category .');
          </script>';
    wp_reset_postdata();

    echo '</form>';
          
    echo '</br><hr>';
     echo '<div class="add-dest-modal">
            <div class="dest-modal-content">
            <div class="modal-heading">
                <b>Add New Stop </b>
                <span class="closes" >&times;</span>
            </div><hr>';
    echo '<form id="destination-form" method="post">';
    echo '<label for="destination-title">' . __( 'Name of Stop :', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="destination-title" name="destination_title" class="full-width" /><br />';

    echo '<br/><label for="trip-category">' . __( 'Category:', 'your-plugin-textdomain' ) . '</label><br/>';
        
    $args = array(
        'taxonomy' => 'destination_category',
        'name' => 'destination_category',
        'id' => 'destination_category',
        'show_option_none' => 'Select a stop category',
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

    echo '<hr><br/><input type="submit" class="preview button" value="Save Stop"><br><br/>';
    echo '</form> </div> </div> ';
    echo '<br/><br/>';

    render_document_upload_meta_box($post_id, $day);
    echo '</div>';


if(isset($_POST['route_edit'])){
    $counter = $_POST['day'];
    $day_start_address = $_POST['route_start_address'];
    $day_start_address_lat = $_POST['route_start_lat'];
    $day_start_address_lng = $_POST['route_start_lng']; 
    $day_end_address = $_POST['day_end_address']; 
    $day_starting_address = $_POST['day_start_address']; 
    $day_end_address_lat = $_POST['day_end_address_lat'];
    $day_end_address_lng = $_POST['day_end_address_lng'];       
    $end_travel_time =  $_POST['end_travel_time_hour'] . ":" . $_POST['end_travel_time_minute'];      

    // $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$day_start_address_lat,$day_start_address_lng&destination=$day_end_address_lat,$day_end_address_lng&units=imperial&key=".esc_attr(get_option('google_api_key'));
    // $directions_data = wp_remote_get($url);
    // $response_body = wp_remote_retrieve_body($directions_data);
    // $data = json_decode($response_body);
    // $distance = $data->routes[0]->legs[0]->distance->text;
    // $duration = $data->routes[0]->legs[0]->duration->text;
        
        $end_route = "_end_route_" .$counter;
        // $next_mile = "_route_miles_" .$counter;
        $end_time = "_end_route_time_" .$counter;
        $next_title = "_route_title_" .$counter;
        $next_destination_category = "_route_destination_category_" .$counter;
        $next_destination = "_route_destination_" .$counter;
        $next_day_start_address = "_route_day_start_address_" .$counter;
        $next_route_day_start_address_lat = "_route_day_start_address_lat_" .$counter;
        $next_route_day_start_address_lang = "_route_day_start_address_lng_" .$counter;        
        $next_day_end_address = "_route_day_end_address_" .$counter;
        $prev_day_end_address = "_route_day_start_address_" .$counter;
        $next_route_day_end_address_lat = "_route_day_end_address_lat_" .$counter;
        $next_route_day_end_address_lang = "_route_day_end_address_lng_" .$counter;
        $selected_destination_name = "_selected_destinations_" .$counter;
        $selected_activities_name = "_selected_activity_" .$counter;
        $day_description_name = "_day_description_" .$counter;
        $start_address_lat = $day_start_address_lat;
        $start_address_lng = $day_start_address_lng;    

        if(isset($_POST['selected_values']) && $_POST['selected_values'] != ""){

        $selected_values = explode(',', $_POST['selected_values']);

        $selected_destinations_data = array();

        foreach ($selected_values as $selected_destination) {
            $destination_address = get_post_meta($selected_destination, '_destination_address', true);
            $destination_lat = get_post_meta($selected_destination, '_destination_lat', true);
            $destination_lang = get_post_meta($selected_destination, '_destination_lang', true);

         $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$start_address_lat,$start_address_lng&destination=$destination_lat,$destination_lang&units=imperial&key=".esc_attr(get_option('google_api_key'));    

            $directions_data = wp_remote_get($url);
            $response_body = wp_remote_retrieve_body($directions_data);
            $datas = json_decode($response_body);
            // Check if the API request was successful
            if ($datas && isset($datas->routes[0]->legs)) {
                $route_datas = $datas->routes[0]->legs;
             }

            $route_datas_time = $route_datas[0]->duration->text;

            preg_match('/(\d+)\s*hours?/', $route_datas_time, $hours);
            preg_match('/(\d+)\s*mins?/', $route_datas_time, $minutes);

            $h = isset($hours[1]) ? $hours[1] : 0;
            $m = isset($minutes[1]) ? $minutes[1] : 0;

            // Format as H:MM
            $dest_time = sprintf('%d:%02d', $h, $m);

            // Create an array with destination ID as key and its details as value
            $selected_destinations_data[$selected_destination] = array(
                'destination_id' => $selected_destination,
                'address' => $destination_address,
                'lat' => $destination_lat,
                'lng' => $destination_lang,
                'time' => $dest_time,
                'map_data' => $route_datas,
            );
             $start_address_lat = $destination_lat;
             $start_address_lng = $destination_lang;
            }
        }else{
            $selected_destinations_data = array();
        }

          $end_url = "https://maps.googleapis.com/maps/api/directions/json?origin=$start_address_lat,$start_address_lng&destination=$day_end_address_lat,$day_end_address_lng&units=imperial&key=" . esc_attr(get_option('google_api_key'));

            $end_directions_data = wp_remote_get($end_url);
            $end_response_body = wp_remote_retrieve_body($end_directions_data);
            $end_data = json_decode($end_response_body);

            // Check if the API request was successful
            if ($end_data && isset($end_data->routes[0]->legs)) {
                $end_route_data = $end_data->routes[0]->legs;
                $end_route_data_time = $end_route_data[0]->duration->text;

                preg_match('/(\d+)\s*hours?/', $end_route_data_time, $hours);
                preg_match('/(\d+)\s*mins?/', $end_route_data_time, $minutes);

                $h = isset($hours[1]) ? $hours[1] : 0;
                $m = isset($minutes[1]) ? $minutes[1] : 0;

                // Format as H:MM
                $end_route_data_end_time = sprintf('%d:%02d', $h, $m);
                // Assuming $post_id and $end_route are defined somewhere in your code
                update_post_meta($post_id, $end_route, $end_route_data);
                update_post_meta($post_id, $end_time, $end_route_data_end_time);
                echo json_encode(array('success' => true));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Failed to retrieve data from the Google Maps Directions API.'));
            }



            // print_r($end_route_data); exit();
         if(isset($_POST['selected_activity_values']) && $_POST['selected_activity_values'] != ""){

        $selected_activity_values = explode(',', $_POST['selected_activity_values']);

        $selected_activities_data = array();

        foreach ($selected_activity_values as $selected_activities) {
            $destination_address = get_post_meta($selected_activities, '_destination_address', true);
            $destination_lat = get_post_meta($selected_activities, '_destination_lat', true);
            $destination_lang = get_post_meta($selected_activities, '_destination_lang', true);
            $end_travel_time_hour = "act_travel_time_hour_" .$selected_activities;
            $end_travel_time_minute = "act_travel_time_minute_" .$selected_activities;
            $act_time = $_POST[$end_travel_time_hour].":".$_POST[$end_travel_time_minute];

             $act_url = "https://maps.googleapis.com/maps/api/directions/json?origin=$destination_lat,$destination_lang&destination=$day_end_address_lat,$day_end_address_lng&units=imperial&key=".esc_attr(get_option('google_api_key'));    

            $act_directions_data = wp_remote_get($act_url);
            $act_response_body = wp_remote_retrieve_body($act_directions_data);
            $act_data = json_decode($act_response_body);
            // Check if the API request was successful
            if ($act_data && isset($act_data->routes[0]->legs)) {
                $act_route_data = $act_data->routes[0]->legs;
             }

             $act_route_datas_time = $act_route_data[0]->duration->text;

            preg_match('/(\d+)\s*hours?/', $act_route_datas_time, $hours);
            preg_match('/(\d+)\s*mins?/', $act_route_datas_time, $minutes);

            $h = isset($hours[1]) ? $hours[1] : 0;
            $m = isset($minutes[1]) ? $minutes[1] : 0;

            // Format as H:MM
            $act_time = sprintf('%d:%02d', $h, $m);

            // Create an array with destination ID as key and its details as value
            $selected_activities_data[$selected_activities] = array(
                'destination_id' => $selected_activities,
                'address' => $destination_address,
                'lat' => $destination_lat,
                'lng' => $destination_lang,
                'time' => $act_time,
                'map_data' => $act_route_data,

            );
            }
        }else{
            $selected_activities_data = array();
        }

        update_post_meta($post_id, $next_day_start_address, $day_start_address);
        update_post_meta($post_id, $next_route_day_start_address_lat, $day_start_address_lat);
        update_post_meta($post_id, $next_route_day_start_address_lang, $day_start_address_lng);
        // update_post_meta($post_id, $next_mile, $distance);
        update_post_meta($post_id, $next_destination_category, sanitize_text_field( $_POST['destination_category'] ));
        // update_post_meta($post_id, $next_destination, sanitize_text_field( $_POST['destination'] ));
        update_post_meta($post_id, $next_title, sanitize_text_field( $_POST['route_title'] ));
        update_post_meta($post_id, $next_day_end_address, sanitize_text_field( $_POST['day_end_address'] ));
        update_post_meta($post_id, $prev_day_end_address, sanitize_text_field( $_POST['day_start_address'] ));
        update_post_meta($post_id, $next_route_day_end_address_lat, sanitize_text_field( $_POST['day_end_address_lat'] ));
        update_post_meta($post_id, $next_route_day_end_address_lang, sanitize_text_field( $_POST['day_end_address_lng'] ));        
        update_post_meta($post_id, $day_description_name, wp_kses_post( $_POST['day_description'] ));
        for ($i = 1; $i <= 7; $i++) {
    $route_reservation_name = "_route_reservation_" . $i . "_" . $counter;
    $route_site_desc_name = "_route_site_desc_" . $i . "_" . $counter;
    $route_nightly_rate_name = "_route_nightly_rate_" . $i . "_" . $counter;
    $route_special_notes_name = "_route_special_notes_" . $i . "_" . $counter;
    $route_deposit_name = "_route_deposit_" . $i . "_" . $counter;
    $route_balance_due_name = "_route_balance_due_" . $i . "_" . $counter; // New field

    // Check if the input fields are empty
    if(isset($_POST['route_reservation_' . $i])) {
        $reservation_value = sanitize_text_field($_POST['route_reservation_' . $i]);
        if(empty($reservation_value)) {
            delete_post_meta($post_id, $route_reservation_name);
        } else {
            update_post_meta($post_id, $route_reservation_name, $reservation_value);  
        }
    }
    if(isset($_POST['route_site_desc_' . $i])) {
        $site_desc_value = sanitize_text_field($_POST['route_site_desc_' . $i]);
        if(empty($site_desc_value)) {
            delete_post_meta($post_id, $route_site_desc_name);
        } else {
            update_post_meta($post_id, $route_site_desc_name, $site_desc_value);  
        }
    }
    if(isset($_POST['route_nightly_rate_' . $i])) {
        $nightly_rate_value = sanitize_text_field($_POST['route_nightly_rate_' . $i]);
        if(empty($nightly_rate_value)) {
            delete_post_meta($post_id, $route_nightly_rate_name);
        } else {
            update_post_meta($post_id, $route_nightly_rate_name, $nightly_rate_value);  
        }
    }
    if(isset($_POST['route_special_notes_' . $i])) {
        $special_notes_value = sanitize_text_field($_POST['route_special_notes_' . $i]);
        if(empty($special_notes_value)) {
            delete_post_meta($post_id, $route_special_notes_name);
        } else {
            update_post_meta($post_id, $route_special_notes_name, $special_notes_value);  
        }
    }
    if(isset($_POST['route_deposit_' . $i])) {
        $deposit_value = sanitize_text_field($_POST['route_deposit_' . $i]);
        if(empty($deposit_value)) {
            delete_post_meta($post_id, $route_deposit_name);
        } else {
            update_post_meta($post_id, $route_deposit_name, $deposit_value);  
        }
    }
    if(isset($_POST['route_balance_due_' . $i])) {  // New field
        $balance_due_value = sanitize_text_field($_POST['route_balance_due_' . $i]);
        if(empty($balance_due_value)) {
            delete_post_meta($post_id, $route_balance_due_name);
        } else {
            update_post_meta($post_id, $route_balance_due_name, $balance_due_value);  
        }
    }
}

        update_post_meta($post_id, $selected_destination_name, $selected_destinations_data);
        update_post_meta($post_id, $selected_activities_name, $selected_activities_data); 

    echo '<script>window.location.href = "' . admin_url( 'post.php?post='. $post_id .'&action=edit' ) . '";</script>';  
}