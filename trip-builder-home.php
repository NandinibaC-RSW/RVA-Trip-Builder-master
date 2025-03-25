<?php

add_shortcode( 'trip-builder-home', 'wpdocs_bartag_func' );

function wpdocs_bartag_func( $atts ) {
    
    if (!is_user_logged_in() ) {
         wp_redirect(get_site_url());
         exit;
    }

    if (wp_doing_ajax()) {
        return;
    }

    if (isset($_POST['delete_route'])) {
        $trip_stay = $_GET['trip_stay'];
        $post_id = $_POST['delete_route'];
        $destination_id = $_POST['destination_id'];
        $start_latlng = $_POST['start_latlng'];
        $end_latlng = $_POST['end_latlng'];
        if ($start_latlng = 'remove' && $end_latlng = 'remove') {
        
            $selected_destination_name = "_selected_activity_" . $trip_stay;
            $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);

                if (isset($selected_destinations[$destination_id])) {
                unset($selected_destinations[$destination_id]);

                update_post_meta($post_id, $selected_destination_name, $selected_destinations);
                }

        wp_redirect('?trip_id=' . $post_id .'&trip_stay=' . $trip_stay);
        exit;
        }
        $selected_destination_name = "_selected_destinations_" . $trip_stay;
        $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);

        if (isset($selected_destinations[$destination_id])) {
            unset($selected_destinations[$destination_id]);

        }
         foreach ($selected_destinations as $destination_id => $destination_details) {
          $last_latlng = $destination_details['lat']. ',' .$destination_details['lng'];
          $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$start_latlng&destination=$last_latlng&units=imperial&key=".esc_attr(get_option('google_api_key'));    

            $directions_data = wp_remote_get($url);
            $response_body = wp_remote_retrieve_body($directions_data);
            $datas = json_decode($response_body);
            // Check if the API request was successful
            if ($datas && isset($datas->routes[0]->legs)) {
                $route_datas = $datas->routes[0]->legs;
             }

            // Create an array with destination ID as key and its details as value
            $selected_destinations_data[$destination_id] = array(
                'destination_id' => $destination_id,
                'address' => $destination_details['address'],
                'lat' => $destination_details['lat'],
                'lng' => $destination_details['lng'],
                'time' => $destination_details['time'],
                'map_data' => $route_datas,
            );

            $start_latlng = $destination_details['lat']. ',' .$destination_details['lng'];
            // $last_latlng =  $end_latlng;
         }   
            // print_r($selected_destinations_data); exit();
            update_post_meta($post_id, $selected_destination_name, $selected_destinations_data);

        wp_redirect('?trip_id=' . $post_id .'&trip_stay=' . $trip_stay);
        exit;
    }



    ob_start();
    $customer_id = get_current_user_id();
    $user = get_user_by('id', $customer_id);
    $direction_symbols = array(
                                'head' => '↑',
                                'uturn-right' => '↩',
                                'uturn-left' => '↪',
                                'turn-right' => '→',
                                'turn-left' => '←',
                                'turn-slight-right' => '⤤',
                                'turn-slight-left' => '⤦', 
                                'ramp-right' => '⇥',
                                'ramp-left' => '⇤', 
                                'fork-right' => '⇢', 
                                'fork-left' => '⇠', 
                                'merge' => '↔', 
                                'roundabout-right' => '↻',
                                'roundabout-left' => '↺',
                                'straight' => '↑',
                                'keep-right' => '↗',
                                'keep-left' => '↖',
                                'slight-right' => '→',
                                'slight-left' => '←',
                                'sharp-right' => '⇒',
                                'sharp-left' => '⇐',
                                'right' => '→', 
                                'left' => '←', 
                            );

    echo '<div class="container_area">';

    if($user){ 

    $args = array(
        'post_type' => 'trip',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_trip_customer_id',
                'value' => $customer_id,
                'compare' => '=',
            ),
        ),
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $posts = get_posts($args);
    $number_of_posts = count($posts);

    if ($posts) {
      if (isset($_GET['trip_id'])){
        $post_id = $_GET['trip_id'];   
      }else{
        $post_id = $posts[0]->ID;  
      }
     $trip_title = get_the_title($post_id); 
     $trip_description = get_post_meta( $post_id, '_trip_description', true );

     $trip_start_date = get_post_meta( $post_id, '_trip_start_date', true );
     $trip_starting_address = get_post_meta( $post_id, '_trip_starting_address', true );
     $trip_ending_address = get_post_meta( $post_id, '_trip_ending_address', true );
     $trip_phone = get_post_meta( $post_id, '_trip_phone', true );

     if (isset($_GET['trip_id']) && !isset($_GET['trip_stay']) && !isset($_GET['dest'])) {
         $upload_document_trips = get_post_meta($post_id, '_upload_document_trip', true);
                $upload_document_trip = explode(",", $upload_document_trips);
    echo '<div class="content_section">
            <div class="content_area">
             <div class="one_day">
                <div class="one_day_heading">
                     <h4>'. $trip_title .'</h4>
                </div>';
                if ($number_of_posts > 1) {
                echo '<div class="close_btn">
                    <a href="trip-builder-home"><i class="fa-regular fa-circle-xmark"></i></a>
                </div>';
                 }
            echo '</div>
            <div class="one_day_description">
                <p>'. nl2br($trip_description) .'</p>
            </div>';

                // if(!empty($upload_document_trips)){
                //     echo '<div class="upload_btn_wrapper">
                //                     <form class="form_data">';
                //                         echo '<a class="upload_btn" id="uploadButton">Download Docs</a>
                //                     </form>
                //                 </div>';
                // }

            echo '<div class="download_popup" style="display:none;">
                <div class="download-popup-content">
                <span class="close">&times;</span>';
                        echo '<table class="widefat"><tbody>
                        <h4>Download document</h4>';
                    foreach ($upload_document_trip as $value) {
                        $file_path = get_attached_file($value);
                        $file_name = basename($file_path);
                        $file_url = wp_get_attachment_url($value);
                         echo '<tr>
                            <td><b>'. $file_name .'</b></td>
                            <td class="wrap-right">
                            <a class="download_btn" href="'. esc_url($file_url) .'" download>Download</a><br>
                            </td>
                        </tr>';
                    }
                    echo '</tbody>
                        </table></div></div>'; 
              echo  '<div class="venus">
                    <h6>Starting date: '. date('d-m-Y', strtotime($trip_start_date)) .'</h6>
                    <h6>Starting location: '. $trip_starting_address .'</h6>
                </div>
            </div>';

        $day = get_post_meta($post_id, '_trip_day_count', true);
        $trip_end_date = date('Y-m-d', strtotime($trip_start_date . ' + ' . $day . ' days'));
        for ($i=1; $i <= $day; $i++) { 

        $current_route = "_route_" . $i;
        $current_title = "_route_title_" . $i;
        $current_description = "_day_description_".$i;     
        $current_value = get_post_meta($post_id, $current_route, true);
        
        if ($current_value) {
            $current_title = get_post_meta($post_id, $current_title, true);
            $current_description = get_post_meta($post_id, $current_description, true);

            echo'<div class="day_section_part">
            <div class="day_section">
                <a href="?trip_id='. $post_id .'&trip_stay='. $i .'">
                <div class="day_btn">'. $current_title .'<i class="fa-solid fa-chevron-right"></i>
                <p style="font-size:15px;">'.$current_description.'</p>
                </div>
                </a>
            </div>
        </div>';
        } else {
            continue;
        }
    }

   echo '<div class="content_area">
                <div class="venus">
                    <h6>Ending date: '. date($trip_end_date) .'</h6>
                    <h6>Ending location: ' . $trip_ending_address . '</h6>
                </div>
            </div>';
   // if ($number_of_posts > 1) {
   //      echo '<div class="next_pri">
   //              <a href="trip-builder-home" class="btn">Back</a>';               
   //      echo '</div>';
   //      }   
        echo '</div>';

     }elseif (isset($_GET['trip_stay']) && !isset($_GET['dest'])) {

        function numberToWords($number) {
            $words = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
            return $words[$number] ?? '';
        }
        $day = $_GET['trip_stay'];
        $dayInWords = numberToWords($day);

        $day_start_address_name = "_route_day_start_address_" .$day;
        $day_end_address_name = "_route_day_end_address_" .$day;
        $day_end_address_dest_id_name = "_route_day_end_address_dest_id_" .$day;
        $destination_name = "_route_destination_" .$day;
        $route_title_name = "_route_title_" .$day;
        $route_time_name = "_end_route_time_" .$day;
        $end_route_name = "_end_route_" .$day;
        $start_lat_route_name = "_route_day_start_address_lat_" .$day;
        $start_lng_route_name = "_route_day_start_address_lng_" .$day;
        $end_lat_route_name = "_route_day_end_address_lat_" .$day;
        $end_lng_route_name = "_route_day_end_address_lng_" .$day;
        $upload_document_name = "_upload_document_day_" .$day;
        $day_description_name = "_day_description_" .$day;
        $route_reservation_name = "_route_reservation_" .$day;
        $route_site_desc_name = "_route_site_desc_" .$day;
        $route_nightly_rate_name = "_route_nightly_rate_" .$day;
        $route_special_notes_name = "_route_special_notes_" .$day;
        $route_deposit_name = "_route_deposit_" .$day;

        $trip_day_starting_address = get_post_meta($post_id, $day_start_address_name, true);
        $trip_day_ending_address = get_post_meta($post_id, $day_end_address_name, true);
        $day_end_address_dest_id = get_post_meta($post_id, $day_end_address_dest_id_name, true);
        $route_title = get_post_meta($post_id, $route_title_name, true);
        $start_lat = get_post_meta($post_id, $start_lat_route_name, true);
        $start_lng = get_post_meta($post_id, $start_lng_route_name, true);
        $end_lat = get_post_meta($post_id, $end_lat_route_name, true);
        $end_lng = get_post_meta($post_id, $end_lng_route_name, true);
        $destination = get_post_meta($post_id, $destination_name, true);
        $destination_post = get_post($destination);
        $upload_documents = get_post_meta($post_id, $upload_document_name, true);
        $day_description = get_post_meta($post_id, $day_description_name, true);
        $upload_document = explode(",", $upload_documents);
        $route_reservation = get_post_meta($post_id, $route_reservation_name, true);
        $route_site_desc = get_post_meta($post_id, $route_site_desc_name, true);
        $route_nightly_rate = get_post_meta($post_id, $route_nightly_rate_name, true);
        $route_deposit = get_post_meta($post_id, $route_deposit_name, true);
        $route_special_notes = get_post_meta($post_id, $route_special_notes_name, true);
  

    echo '<div class="content_section">
            <div class="one_day">
                <div class="one_day_heading">
                     <h4>'. $route_title .'</h4>
                </div>
                <div class="close_btn">
                    <a href="?trip_id='. $post_id .'"><i class="fa-regular fa-circle-xmark"></i></a>
                </div>
            </div>
            <div class="one_day_description">
                <p>'. nl2br($day_description) .'</p>
            </div>';


            echo '<div class="download_popup" style="display:none;">
                <div class="download-popup-content">
                <span class="close">&times;</span>';
                        echo '<table class="widefat"><tbody>
                        <h4>Download document</h4>';
                    foreach ($upload_document as $value) {
                        $file_path = get_attached_file($value);
                        $file_name = basename($file_path);
                        $file_url = wp_get_attachment_url($value);
                         echo '<tr>
                            <td><b>'. $file_name .'</b></td>
                            <td class="wrap-right">
                            <a class="download_btn" href="'. esc_url($file_url) .'" download>Download</a><br>
                            </td>
                        </tr>';
                    }
                    echo '</tbody>
                        </table></div></div>'; 
            echo '<div class="address_section">
                <div class="address_text">
                    <h4>Starting Address:</h4>
                    <p> '. $trip_day_starting_address .'</p>
                </div>
                <div class="down_arrow">
                    <i class="fa-solid fa-arrow-turn-down"></i>
                </div>
            </div>';

            // Retrieve selected destinations for the specific day
            $selected_destination_name = "_selected_destinations_" . $day;
            $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);
            // $destination_category_name = "_route_destination_category_" . $day;
            // $destination_category = get_post_meta($post_id, $destination_category_name, true);
            // $term = get_term($destination_category, 'destination_category');

            // Check if there are selected destinations
            if (!empty($selected_destinations)) {
                echo '<div class="one_day">
                         <h4>Stops:</h4>
                      </div>';
                $counter = 0;
                foreach ($selected_destinations as $destination_id => $destination_details) {
                    $destination_post = get_post($destination_id);
                    $destination_address = $destination_details['address'];
                    $destination_description = get_post_meta($destination_id, '_destination_description', true);
                    $dest_phone = get_post_meta($destination_id, '_dest_phone', true);
                    $dest_website = get_post_meta($destination_id, '_dest_website', true);

                    echo '<div class="destination_section">';
                    echo ' <div class="dest_box">
                            <div class="dest_box_content">
                                <h6> ' . esc_html($destination_post->post_title) . '</h6>
                                <input type="hidden" id="start_latlng" value="' . $start_lat . ',' . $start_lng . '"> 
                                <input type="hidden" id="end_latlng" value="' . $end_lat . ',' . $end_lng . '"> 
                            </div> 
                            <div class="dest_box_icon">
                              <a href="?trip_id='. $post_id .'&trip_stay=' . $day .'&dest=' . $destination_id .'">  
                                <i class="fa-solid fa-circle-info"></i>
                              </a>  
                              <a class="remove_popup_btn" data-destination-id="' . $destination_id . '">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                              </a>    
                            </div>
                        </div>';
                    // echo '<div class="remove_popup" style="display:none;">
                    //         <div class="remove-popup-content">
                    //             <p>Are you sure you want to remove <br><span>' . esc_html($destination_post->post_title) . '</span><br> from your itinerary for <span>' . esc_html($route_title) . '</span>?</p>
                    //             <p>Please Note, this change is permanent and <br> will amend your trun-by-turn directions <br> for the rest of the day.</p>
                    //             <form method="post">
                    //             <input type="hidden" name="destination_id" value="'.esc_attr($destination_id) .'">
                    //             <a class="cancel_remove_btn">Go Back</a>
                    //            <button class="confirm_remove_btn" type="submit" name="delete_route" value="'. $post_id .'">Remove</button></form>
                    //         </div>
                    //     </div>';
                    
                     echo ' </div>';
                    // $response_data = get_post_meta($post_id, '_direction_data_'.$post_id.'_'.$day, true);
                    // $response_json = json_decode($response_data);

                    if ($destination_details['map_data']) {
                    
                    foreach ($destination_details['map_data'] as $directiondata) {
                        list($dest_hour, $dest_minute) = explode(":", $destination_details['time']);
                         echo '<div class="directions_section">';
                        echo '<h6 class="heading">Turn-by-Turn Directions:</h6><h6 class="est_heading">Est. Travel Time: '. $dest_hour .' Hours ' . $dest_minute . ' Minutes<span class="circle-icon"><i class="fas fa-chevron-down"></i></span></h6>';
                        echo '<div class="directions_content">';

                        // Display starting location
                        echo '<div class="distance_part">
                                <div class="above_arrow">
                                    <span style="font-size:30px;">🏁</span>
                                </div>';
                        echo '<div class="place_name">
                                <p><b>' . $directiondata->start_address . '</b></p>
                            </div>
                        </div>';

                        foreach ($directiondata->steps as $step) {
                            if (!empty($step->maneuver)) {
                                $text_direction = strtolower($step->maneuver);
                                $direction_symbol = isset($direction_symbols[$text_direction]) ? $direction_symbols[$text_direction] : '';
                            } else {
                                $direction_symbol = "";
                            }
                            echo '<div class="distance_part">
                                    <div class="above_arrow">
                                        <span style="font-size:30px;">' . $direction_symbol . '</span>
                                    </div>';
                            echo '<div class="place_name">
                                    <p>' . $step->html_instructions . '</p>
                                    <span>' . $step->duration->text . ' ( ' . $step->distance->text . ' )</span>
                                </div>
                            </div>';
                        }

                        // Display ending location
                        echo '<div class="distance_part">
                                <div class="above_arrow">
                                    <span style="font-size:30px;">🏁</span>
                                </div>';
                        echo '<div class="place_name">
                                <p><b>' . $directiondata->end_address . '</b></p>
                            </div>
                        </div>';

                        echo '</div></div><hr>';
                    }
                }
              }
            }        
            echo '<div class="remove_popup" style="display:none;">
                    <div class="remove-popup-content">
                        <p>Are you sure you want to remove <br><span class="destination-title"></span>?</p>
                        <p>Please Note, this change is permanent and <br> will amend your turn-by-turn directions <br> for the rest of the day.</p>
                        <form method="post">
                            <input type="hidden" name="destination_id" value="">
                            <input type="hidden" name="start_latlng" value="">
                            <input type="hidden" name="end_latlng" value="">
                            <a class="cancel_remove_btn">Cancel</a>
                            <button class="confirm_remove_btn" type="submit" name="delete_route" value="'. $post_id .'">Remove</button>
                        </form>
                    </div>
                </div>';

            if ($day_end_address_dest_id) {
                $destination_post = get_post($day_end_address_dest_id);
                $destination_title = $destination_post->post_title;

                echo '<div class="address_section">
                        <div class="address_box_text">
                        <h4>Destination :</h4>
                        <div class="box">
                            <div class="box_content">
                                 <div class="directions_section">
                                <h6> '. $destination_title .' 
                                <div class="box_icon">
                                  <a href="?trip_id='. $post_id .'&trip_stay=' . $day .'&dest=' . $day_end_address_dest_id .'">  
                                    <i class="fa-solid fa-circle-info"></i>
                                  </a>
                                </div>
                                <span class="circle-icon"><i class="fas fa-chevron-down"></i></span></h6>';
                echo '<div class="directions_content">';
                echo ' <span>' . $trip_day_ending_address . '</span><br>';

                function render_rv_fields($rv_number, $day, $post_id) {
                    // Retrieve saved values if they exist
                    $route_reservation_name = "_route_reservation_" . $rv_number . "_" . $day;
                    $route_site_desc_name = "_route_site_desc_" . $rv_number . "_" . $day;
                    $route_nightly_rate_name = "_route_nightly_rate_" . $rv_number . "_" . $day;
                    $route_special_notes_name = "_route_special_notes_" . $rv_number . "_" . $day;
                    $route_balance_due_name = "_route_balance_due_" . $rv_number . "_" . $day;
                    $route_deposit_name = "_route_deposit_" . $rv_number . "_" . $day;

                    $route_reservation = get_post_meta($post_id, $route_reservation_name, true);
                    $route_site_desc = get_post_meta($post_id, $route_site_desc_name, true);
                    $route_nightly_rate = get_post_meta($post_id, $route_nightly_rate_name, true);
                    $route_deposit = get_post_meta($post_id, $route_deposit_name, true);
                    $route_special_notes = get_post_meta($post_id, $route_special_notes_name, true);
                    $route_balance_due = get_post_meta($post_id, $route_balance_due_name, true);
                    // Check if all inputs are empty
                    if (empty($route_reservation) && empty($route_site_desc) && empty($route_nightly_rate) && empty($route_deposit) && empty($route_special_notes) && empty($route_balance_due)) {
                        return false; // Skip rendering if all inputs are empty
                    }

                    echo '<div class="rv-box" style="width: 45%;margin: 1%;border-right:1px solid black;padding: 10px;border: 1px solid #ddd;box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">';
                    echo '<h3 style="font-size:26px;">RV ' . $rv_number . '</h3>';

                    if (!empty($route_reservation)) {
                        echo '<label style="font-weight:bold;">Reservation:</label><br><span class="half-width" id="route_reservation_' . $rv_number . '" name="route_reservation_' . $rv_number . '">' . esc_attr($route_reservation) . '</span></br>';
                    }

                    if (!empty($route_site_desc)) {
                        echo '<label style="font-weight:bold;">Site description:</label><br><span class="half-width" id="route_site_desc_' . $rv_number . '" name="route_site_desc_' . $rv_number . '">' . esc_attr($route_site_desc) . '</span></br>';
                    }

                    if (!empty($route_nightly_rate)) {
                        echo '<label style="font-weight:bold;">Nightly Rate:</label><br><span class="half-width" id="route_nightly_rate_' . $rv_number . '" name="route_nightly_rate_' . $rv_number . '">' . esc_attr($route_nightly_rate) . '</span></br>';
                    }

                    if (!empty($route_deposit)) {
                        echo '<label style="font-weight:bold;">Deposit:</label><br><span class="half-width" id="route_deposit_' . $rv_number . '" name="route_deposit_' . $rv_number . '">' . esc_attr($route_deposit) . '</span></br>';
                    }

                    if (!empty($route_balance_due)) {
                        echo '<label style="font-weight:bold;">Balance Due:</label><br><span class="half-width" id="route_balance_due_' . $rv_number . '" name="route_balance_due_' . $rv_number . '">' . esc_attr($route_balance_due) . '</span></br>';
                    }
                    
                    if (!empty($route_special_notes)) {
                        echo '<label style="font-weight:bold;">Special Notes:</label><br><span class="half-width" id="route_special_notes_' . $rv_number . '" name="route_special_notes_' . $rv_number . '">' . esc_attr($route_special_notes) . '</span></br>';
                    }


                    echo '</div>';
                    return true;
                }

                $rv_count = 0; // Initialize RV count
                echo '<div class="rv-container" style="display: flex;flex-wrap: wrap;">';
                for ($i = 1; $i <= 7; $i++) {
                    if (render_rv_fields($i, $day, $post_id)) {
                        $rv_count++; // Increment count for each rendered RV box
                    }
                }
                echo '</div>';
                echo '<input type="hidden" id="rv_count" name="rv_count" value="' . $rv_count . '">'; // Add hidden input field with RV count
                echo '</div>
                        </div>
                    </div>
                    </div>';

                // Add JavaScript to adjust the width of the last RV box if necessary
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var rvCount = ' . $rv_count . ';
                        if ([1, 3, 5, 7].includes(rvCount)) {
                            var rvBoxes = document.querySelectorAll(".rv-box");
                            if (rvBoxes.length > 0) {
                                rvBoxes[rvBoxes.length - 1].style.width = "92%";
                            }
                        }
                    });
                </script>';

            }else {
                    // Code for displaying end address when destination is not selected
                    $end_route_data = get_post_meta($post_id, $end_route_name, true);
                    $route_time = get_post_meta($post_id, $route_time_name, true);
                    list($end_hour, $end_minute) = explode(":", $route_time);
                    
                    if ($end_route_data) {
                        foreach ($end_route_data as $directiondata) {
                            echo '<div class="address_section">
                                    <div class="address_box_text">
                                        <h4>Final Destination:</h4>
                                        <div class="box">
                                            <div class="box_content">
                                                <div class="directions_section">
                                                    <h6>' . $directiondata->end_address . '<span class="circle-icon"><i class="fas fa-chevron-down"></i></span></h6>';
                                echo '<div class="directions_content">';
                                echo ' <span>' . $trip_day_ending_address . '</span><br>';

                                function render_rv_fields($rv_number, $day, $post_id) {
                                    // Retrieve saved values if they exist
                                    $route_reservation_name = "_route_reservation_" . $rv_number . "_" . $day;
                                    $route_site_desc_name = "_route_site_desc_" . $rv_number . "_" . $day;
                                    $route_nightly_rate_name = "_route_nightly_rate_" . $rv_number . "_" . $day;
                                    $route_special_notes_name = "_route_special_notes_" . $rv_number . "_" . $day;
                                    $route_deposit_name = "_route_deposit_" . $rv_number . "_" . $day;
                                    $route_balance_due_name = "_route_balance_due_" . $rv_number . "_" . $day;

                                    $route_reservation = get_post_meta($post_id, $route_reservation_name, true);
                                    $route_site_desc = get_post_meta($post_id, $route_site_desc_name, true);
                                    $route_nightly_rate = get_post_meta($post_id, $route_nightly_rate_name, true);
                                    $route_deposit = get_post_meta($post_id, $route_deposit_name, true);
                                    $route_special_notes = get_post_meta($post_id, $route_special_notes_name, true);
                                    $route_balance_due = get_post_meta($post_id, $route_balance_due_name, true);

                                    // Check if all inputs are empty
                                    if (empty($route_reservation) && empty($route_site_desc) && empty($route_nightly_rate) && empty($route_deposit) && empty($route_special_notes) && empty($route_balance_due)) {
                                        return false; // Skip rendering if all inputs are empty
                                    }

                                    echo '<div class="rv-box" style="width: 45%;margin: 1%;border-right:1px solid black;padding: 10px;border: 1px solid #ddd;box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">';
                                    echo '<h3 style="font-size:26px;">RV ' . $rv_number . '</h3>';

                                    if (!empty($route_reservation)) {
                                        echo '<label style="font-weight:bold;">Reservation:</label><br><span class="half-width" id="route_reservation_' . $rv_number . '" name="route_reservation_' . $rv_number . '">' . esc_attr($route_reservation) . '</span></br>';
                                    }

                                    if (!empty($route_site_desc)) {
                                        echo '<label style="font-weight:bold;">Site description:</label><br><span class="half-width" id="route_site_desc_' . $rv_number . '" name="route_site_desc_' . $rv_number . '">' . esc_attr($route_site_desc) . '</span></br>';
                                    }

                                    if (!empty($route_nightly_rate)) {
                                        echo '<label style="font-weight:bold;">Nightly Rate:</label><br><span class="half-width" id="route_nightly_rate_' . $rv_number . '" name="route_nightly_rate_' . $rv_number . '">' . esc_attr($route_nightly_rate) . '</span></br>';
                                    }

                                    if (!empty($route_deposit)) {
                                        echo '<label style="font-weight:bold;">Deposit:</label><br><span class="half-width" id="route_deposit_' . $rv_number . '" name="route_deposit_' . $rv_number . '">' . esc_attr($route_deposit) . '</span></br>';
                                    }

                                    if (!empty($route_balance_due)) {
                                        echo '<label style="font-weight:bold;">Balance Due:</label><br><span class="half-width" id="route_balance_due_' . $rv_number . '" name="route_balance_due_' . $rv_number . '">' . esc_attr($route_balance_due) . '</span></br>';
                                    }

                                    if (!empty($route_special_notes)) {
                                        echo '<label style="font-weight:bold;">Special Notes:</label><br><span class="half-width" id="route_special_notes_' . $rv_number . '" name="route_special_notes_' . $rv_number . '">' . esc_attr($route_special_notes) . '</span></br>';
                                    }

                                    echo '</div>';
                                    return true;
                                }

                                $rv_count = 0; // Initialize RV count
                                echo '<div class="rv-container" style="display: flex;flex-wrap: wrap;">';
                                for ($i = 1; $i <= 7; $i++) {
                                    if (render_rv_fields($i, $day, $post_id)) {
                                        $rv_count++; // Increment count for each rendered RV box
                                    }
                                }
                                echo '</div>';
                                echo '<input type="hidden" id="rv_count" name="rv_count" value="' . $rv_count . '">'; // Add hidden input field with RV count
                                echo '</div>
                                        </div>
                                    </div>
                                    </div>';

                                // Add JavaScript to adjust the width of the last RV box if necessary
                                echo '<script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        var rvCount = ' . $rv_count . ';
                                        if ([1, 3, 5, 7].includes(rvCount)) {
                                            var rvBoxes = document.querySelectorAll(".rv-box");
                                            if (rvBoxes.length > 0) {
                                                rvBoxes[rvBoxes.length - 1].style.width = "92%";
                                            }
                                        }
                                    });
                                </script>';

                        }
                    }
                }            
            
        $end_route_data = get_post_meta($post_id, $end_route_name, true);
        $route_time = get_post_meta($post_id, $route_time_name, true);
        list($end_hour, $end_minute) = explode(":", $route_time);
        if ($end_route_data) {
        foreach ($end_route_data as $directiondata) {
            echo '<div class="directions_section">';
            echo '<h6 class="heading">Turn-by-Turn Directions:</h6><h6 class="est_heading">Est. Travel Time: '. $end_hour .' Hours ' . $end_minute . ' Minutes<span class="circle-icon"><i class="fas fa-chevron-down"></i></span></h6>';
            echo '<div class="directions_content">';

            // Display starting location
            echo '<div class="distance_part">
                    <div class="above_arrow">
                        <span style="font-size:30px;">🏁</span>
                    </div>';
            echo '<div class="place_name">
                    <p><b>' . $directiondata->start_address . '</b></p>
                </div>
            </div>';

            foreach ($directiondata->steps as $step) {
                if (!empty($step->maneuver)) {
                    $text_direction = strtolower($step->maneuver);
                    $direction_symbol = isset($direction_symbols[$text_direction]) ? $direction_symbols[$text_direction] : '';
                } else {
                    $direction_symbol = "";
                }
                echo '<div class="distance_part">
                        <div class="above_arrow">
                            <span style="font-size:30px;">' . $direction_symbol . '</span>
                        </div>';
                echo '<div class="place_name">
                        <p>' . $step->html_instructions . '</p>
                        <span>' . $step->duration->text . ' ( ' . $step->distance->text . ' )</span>
                    </div>
                </div>';
            }

            // Display ending location
            echo '<div class="distance_part">
                    <div class="above_arrow">
                        <span style="font-size:30px;">🏁</span>
                    </div>';
            echo '<div class="place_name">
                    <p><b>' . $directiondata->end_address . '</b></p>
                </div>
            </div>';

            echo '</div></div><hr>';
            }
        }


         $selected_activity_name = "_selected_activity_" . $day;
         $selected_activity = get_post_meta($post_id, $selected_activity_name, true);
        if (!empty($selected_activity)) {
            echo '<div class="one_day center">
                    <h4>While Youre in Area...</h4>
                  </div>';
       
            $counter = 0;
                foreach ($selected_activity as $destination_id => $destination_details) {
                    $destination_post = get_post($destination_id);
                    $destination_address = $destination_details['address'];
                    $destination_description = get_post_meta($destination_id, '_destination_description', true);
                    $dest_phone = get_post_meta($destination_id, '_dest_phone', true);
                    $dest_website = get_post_meta($destination_id, '_dest_website', true);

                    echo '<div class="destination_section">';
                    echo ' <div class="dest_box">
                            <div class="dest_box_content">
                                <h6> ' . esc_html($destination_post->post_title) . '</h6>
                                 <input type="hidden" id="start_latlng" value="remvoe"> 
                                <input type="hidden" id="end_latlng" value="remove"> 
                            </div> 
                            <div class="dest_box_icon">
                              <a href="?trip_id='. $post_id .'&trip_stay=' . $day .'&dest=' . $destination_id .'">  
                                <i class="fa-solid fa-circle-info"></i>
                              </a>  
                              <a class="remove_popup_btn" data-destination-id="' . $destination_id . '">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                              </a>    
                            </div>
                        </div>';
                    
                     echo ' </div>';

                    // $response_data = get_post_meta($post_id, '_direction_data_'.$post_id.'_'.$day, true);
                    // $response_json = json_decode($response_data);
                    
               if ($destination_details['map_data']) {
 
                foreach ($destination_details['map_data'] as $directiondata) {
                        list($act_hour, $act_minute) = explode(":", $destination_details['time']);
                        echo '<div class="directions_section">';
                        echo '<h6 class="heading">Turn-by-Turn Directions:</h6><h6 class="est_heading">Est. Travel Time: '. $act_hour .' Hours ' . $act_minute . ' Minutes<span class="circle-icon"><i class="fas fa-chevron-down"></i></span></h6>';
                        echo '<div class="directions_content">';

                        // Display starting location
                        echo '<div class="distance_part">
                                <div class="above_arrow">
                                    <span style="font-size:30px;">🏁</span>
                                </div>';
                        echo '<div class="place_name">
                                <p><b>' . $directiondata->start_address . '</b></p>
                            </div>
                        </div>';

                        foreach ($directiondata->steps as $step) {
                            if (!empty($step->maneuver)) {
                                $text_direction = strtolower($step->maneuver);
                                $direction_symbol = isset($direction_symbols[$text_direction]) ? $direction_symbols[$text_direction] : '';
                            } else {
                                $direction_symbol = "";
                            }
                            echo '<div class="distance_part">
                                    <div class="above_arrow">
                                        <span style="font-size:30px;">' . $direction_symbol . '</span>
                                    </div>';
                            echo '<div class="place_name">
                                    <p>' . $step->html_instructions . '</p>
                                    <span>' . $step->duration->text . ' ( ' . $step->distance->text . ' )</span>
                                </div>
                            </div>';
                        }

                        // Display ending location
                        echo '<div class="distance_part">
                                <div class="above_arrow">
                                    <span style="font-size:30px;">🏁</span>
                                </div>';
                        echo '<div class="place_name">
                                <p><b>' . $directiondata->end_address . '</b></p>
                            </div>
                        </div>';

                        echo '</div></div><hr>';
                    }
                }      
            }
        }         

            if(!empty($upload_documents)){
                echo '<div class="upload_btn_wrapper">
                        <form class="form_data">';
                            echo '<a class="upload_btn" id="uploadButton">Download Documents</a>
                        </form>
                    </div>';
            }              
           echo '</div>';

     }elseif (isset($_GET['trip_stay']) && isset($_GET['dest'])) {
        
         if (isset($_GET['dest']) ) {
                // $destination_details = $selected_destinations[$_GET['dest']];
                $destination_id = $_GET['dest'];
                $route_title_name = "_route_title_" . $_GET['trip_stay'];
                $dest_doc_name = "_destination_document";
                $route_title = get_post_meta($post_id, $route_title_name, true);
                $destination_post = get_post($destination_id);
                $destination_title = $destination_post->post_title;
                $destination_address = get_post_meta( $destination_id, '_destination_address', true );
                $destination_description = get_post_meta($destination_id, '_destination_description', true);
                $dest_phone = get_post_meta($destination_id, '_dest_phone', true);
                $dest_website = get_post_meta($destination_id, '_dest_website', true);
                $attachment_id = get_post_thumbnail_id($destination_id);
                $image_url = esc_attr( get_post_meta( $destination_id, '_destination_image', true ) );
                $file_url = $image_url ? $image_url : plugin_dir_url(__FILE__).'/assets/img/logo_img.png';

      echo '<div class="content_section">
             <div class="one_day">
                <div class="one_day_heading">
                    <h4>' . esc_html($destination_title) . '</h4>
                </div>
                <div class="close_btn">
                    <a href="?trip_id='. $post_id .'&trip_stay=' . $_GET['trip_stay'] .'"><i class="fa-regular fa-circle-xmark"></i></a>
                </div>
            </div>';

            // $selected_destination_name = "_selected_destinations_" . $_GET['trip_stay'];
            // $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);


            echo '<div class="Interest_details">
                
                <img src="' . esc_url($file_url) . '" alt="' . esc_html($destination_title) . '" height="auto" width="100%">
            </div>';
            echo '<div class="destination_section">
                    <div class="destinations_area">
                        
                    </div>
                    <div class="btn_section">
                    <div class="location_icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="map_location">
                            <h4>' . esc_html($destination_address) . '</h4>
                        <a href="tel:' . $dest_phone . '" class="btn">Call</a>
                        <a href="' . $dest_website . '" target="_new" class="btn">website</a>
                      </div>
                     </div><hr>
                </div>';
            echo '</ hr>
            <div>
                <p>' . esc_html($destination_description) . '</p>
            </div>';

              $dest_guide = get_post_meta($destination_id, $dest_doc_name, true);
              if ($dest_guide) {
                  
              $dest_guide = explode(",", $dest_guide);
            echo '<center><br><a id="guide_doc" class="download_guide_btn">Download Guide</a></center>

                <div class="download_popup" id="guide_doc_model" style="display:none;">
                <div class="download-popup-content">
                <span class="close">&times;</span>';
                        echo '<table class="widefat"><tbody>
                        <h4>Download document</h4>';
                    foreach ($dest_guide as $value) {
                        $file_path = get_attached_file($value);
                        $file_name = basename($file_path);
                        $file_url = wp_get_attachment_url($value);
                         echo '<tr>
                            <td><b>'. $file_name .'</b></td>
                            <td class="wrap-right">
                            <a class="download_btn" href="'. esc_url($file_url) .'" download>Download</a><br>
                            </td>
                        </tr>';
                    }
                    echo '</tbody>
                        </table></div></div>'; 
                }        
       }         
    }else{

        if ($number_of_posts > 1) {

        echo '<div class="content_section">
               <div class="one_day">
                <div class="one_day_heading">
                     <h3>Welcome '. $user->display_name .'</h3>
                    <p>Select your trip below to get started!</p>
                </div>
              </div>
            </div>';
    foreach ($posts as $post) {
    
        echo'<div class="day_section_part">
            <div class="day_section">
                <a href="?trip_id='. $post->ID .'">
                <div class="day_btn">'. $post->post_title .'<i class="fa-solid fa-chevron-right"></i>
                
                </div>
                </a>
            </div>
        </div>';
      }    
     echo'</div>';
     }else{
        $upload_document_trips = get_post_meta($post_id, '_upload_document_trip', true);
                $upload_document_trip = explode(",", $upload_document_trips);
    echo '<div class="content_section">
            <div class="content_area">
                <h4> ' . $trip_title  . ' </h4>
                <p>'. $trip_description .'</p>';

                // if(!empty($upload_document_trips)){
                //     echo '<div class="upload_btn_wrapper">
                //                     <form class="form_data">';
                //                         echo '<a class="upload_btn" id="uploadButton">Download Docs</a>
                //                     </form>
                //                 </div>';
                // }

            echo '<div class="download_popup" style="display:none;">
                <div class="download-popup-content">
                <span class="close">&times;</span>';
                        echo '<table class="widefat"><tbody>
                        <h4>Download document</h4>';
                    foreach ($upload_document_trip as $value) {
                        $file_path = get_attached_file($value);
                        $file_name = basename($file_path);
                        $file_url = wp_get_attachment_url($value);
                         echo '<tr>
                            <td><b>'. $file_name .'</b></td>
                            <td class="wrap-right">
                            <a class="download_btn" href="'. esc_url($file_url) .'" download>Download</a><br>
                            </td>
                        </tr>';
                    }
                    echo '</tbody>
                        </table></div></div>'; 
              echo  '<div class="venus">
                    <h6>Starting date: '. date('d-m-Y', strtotime($trip_start_date)) .'</h6>
                    <h6>Starting location: '. $trip_starting_address .'</h6>
                </div>
            </div>';

        $day = get_post_meta($post_id, '_trip_day_count', true);
        $trip_end_date = date('Y-m-d', strtotime($trip_start_date . ' + ' . $day . ' days'));
        for ($i=1; $i <= $day; $i++) { 

        $current_route = "_route_" . $i;
        $current_title = "_route_title_" . $i;   
        $current_description = "_day_description_".$i;  
        $current_value = get_post_meta($post_id, $current_route, true);
        
        if ($current_value) {
            $current_title = get_post_meta($post_id, $current_title, true);
            $current_description = get_post_meta($post_id, $current_description, true);

            echo'<div class="day_section_part">
            <div class="day_section">
                <a href="?trip_id='. $post_id .'&trip_stay='. $i .'">
                <div class="day_btn">'. $current_title .'<i class="fa-solid fa-chevron-right"></i>
                <p style="font-size:15px;">'.$current_description.'</p>
                </div>
                </a>
            </div>
        </div>';
        } else {
            continue;
        }
    }

   echo '<div class="content_area">
                <div class="venus">
                    <h6>Ending date: '. date($trip_end_date) .'</h6>
                    <h6>Ending location: ' . $trip_ending_address . '</h6>
                </div>
            </div>
        </div>';
         }
        }
     }else{

        echo '<div class="content_section">
                <div class="content_area">
                    <h4> TRIP NOT FOUND</h4>
              </div>
            </div>';
        }
    }
    else{

        echo '<div class="content_section">
                <div class="content_area">
                    <h4> USER DATA NOT FOUND</h4>
              </div>
            </div>';

    }
    echo '</div>';
return ob_get_clean(); 
}