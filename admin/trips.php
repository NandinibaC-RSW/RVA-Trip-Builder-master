<?php

// Define custom post type
function custom_trip_post_type() {
    $labels = array(
        'name'               => _x( 'Trip Buidler', 'post type general name', 'your-plugin-textdomain' ),
        'singular_name'      => _x( 'Trip', 'post type singular name', 'your-plugin-textdomain' ),
        'menu_name'          => _x( 'Trip Builder', 'admin menu', 'your-plugin-textdomain' ),
        'add_new'            => __( 'Add Trip', 'your-plugin-textdomain' ),
        'add_new_item'       => __( 'Add New Trip', 'your-plugin-textdomain' ),
        'edit_item'          => __( 'Edit Trip', 'your-plugin-textdomain' ),
        'new_item'           => __( 'New Trip', 'your-plugin-textdomain' ),
        'view_item'          => __( 'View Trip', 'your-plugin-textdomain' ),
        'search_items'       => __( 'Search Trips', 'your-plugin-textdomain' ),
        'not_found'          => __( 'No trips found', 'your-plugin-textdomain' ),
        'not_found_in_trash' => __( 'No trips found in Trash', 'your-plugin-textdomain' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_position'      => 20,
        'supports'           => array( 'title' ),
        'rewrite'            => array( 'slug' => 'trip' ),
    );

    register_post_type( 'trip', $args );
}
add_action( 'init', 'custom_trip_post_type' );


// Add metabox
function trip_details_metabox() {
    add_meta_box(
        'trip-details',
        __( 'Trip Details', 'your-plugin-textdomain' ),
        'render_trip_details_metabox',
        'trip',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'trip_details_metabox' );



function remove_yoast_seo_metabox() {
    remove_meta_box('wpseo_meta', 'trip', 'normal');
    remove_meta_box('wpseo_meta', 'destination', 'normal');
}
add_action('add_meta_boxes', 'remove_yoast_seo_metabox', 11);



function render_trip_details_metabox( $post ) {
    // Retrieve saved values

    $trip_notes = get_post_meta( $post->ID, '_trip_notes', true );
    $trip_description = get_post_meta( $post->ID, '_trip_description', true );
    $trip_start_date = get_post_meta( $post->ID, '_trip_start_date', true );
    $trip_starting_address = get_post_meta( $post->ID, '_trip_starting_address', true );
    $trip_starting_address_lat = get_post_meta( $post->ID, '_trip_starting_address_lat', true );
    $trip_starting_address_lang = get_post_meta( $post->ID, '_trip_starting_address_lang', true );
    $trip_starting_address_lang = get_post_meta( $post->ID, '_trip_starting_address_lang', true );
    $trip_customer_id = get_post_meta( $post->ID, '_trip_customer_id', true );
    $trip_customer_first_name = get_post_meta( $post->ID, '_trip_customer_first_name', true );
    $trip_customer_last_name = get_post_meta( $post->ID, '_trip_customer_last_name', true );
    $trip_phone = get_post_meta( $post->ID, '_trip_phone', true );
    $trip_email = get_post_meta( $post->ID, '_trip_email', true );
    $trip_password = get_post_meta( $post->ID, '_trip_password', true );
    $trip_ending_address = get_post_meta( $post->ID, '_trip_ending_address', true );
    $trip_ending_address_lat = get_post_meta( $post->ID, '_trip_ending_address_lat', true );
    $trip_ending_address_lang = get_post_meta( $post->ID, '_trip_ending_address_lang', true );

    // Output the fields

    echo '<label for="trip-notes">' . __( 'Notes : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<textarea id="trip-notes" name="trip_notes" placeholder="Notes are only seen by administrator" class="full-width" rows="2">' . esc_textarea( $trip_notes ) . '</textarea><br /><br/>';
    
    echo '<label for="trip-description">' . __( 'Description : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<textarea id="trip-description" name="trip_description" placeholder="Description is seen on the customer&#39;s page" class="full-width" rows="4">' . esc_textarea( $trip_description ) . '</textarea><br /><br/>';

    echo '<label for="trip-start-date">' . __( 'Start Date : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input class="full-width" type="date" id="trip-start-date" name="trip_start_date" value="' . esc_attr( $trip_start_date ) . '" /><br /><br/>';

    echo '<label for="trip_starting_address">' . __( 'Starting Address : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="trip_starting_address" name="trip_starting_address" value="' . esc_attr( $trip_starting_address ) . '" class="full-width" /><input type="hidden" id="starting_address_lat" name="trip_starting_address_lat" value="' . esc_attr( $trip_starting_address_lat ) . '">
          <input type="hidden" id="starting_address_lang" name="trip_starting_address_lang" value="' . esc_attr( $trip_starting_address_lang ) . '"><br /><br/>';

    echo '<label for="trip_ending_address">' . __( 'Ending Address : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="trip_ending_address" name="trip_ending_address" value="' . esc_attr( $trip_ending_address ) . '" class="full-width" /> <input type="hidden" id="ending_address_lat" name="trip_ending_address_lat" value="' . esc_attr( $trip_ending_address_lat ) . '">
          <input type="hidden" id="ending_address_lang" name="trip_ending_address_lang" value="' . esc_attr( $trip_ending_address_lang ) . '">
          <br /><br/>';
          pos_get_all_users($trip_customer_id);

    echo '<br /><br/><div class="customer-div"><label for="trip-customer-first-name">' . __( 'Customer First Name : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="trip-customer-first-name" name="trip_customer_first_name" value="' . esc_attr( $trip_customer_first_name ) . '" class="full-width" /><br /><br/>';

    echo '<label for="trip-customer-last-name">' . __( 'Customer Last Name : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="trip-customer-last-name" name="trip_customer_last_name" value="' . esc_attr( $trip_customer_last_name ) . '" class="full-width" /><br /><br/>';

    echo '<label for="trip-phone">' . __( 'Phone : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="trip-phone" name="trip_phone" value="' . esc_attr( $trip_phone ) . '" class="full-width" /><br /><br/>';

    echo '<label for="trip-email">' . __( 'Email : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="email" id="trip-email" name="trip_email" value="' . esc_attr( $trip_email ) . '" class="full-width" /><br /><br/>';

    echo '<label for="trip-password">' . __( 'Password : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="password" id="trip-password" name="trip_password" value="' . esc_attr( $trip_password ) . '" class="full-width" /></div><br /><br/>';
   
    //render_trip_document_upload_meta_box($post->ID);
}



function save_trip_details_metabox( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if ( isset( $_POST['trip_notes'] ) ) {
        update_post_meta( $post_id, '_trip_notes', sanitize_text_field( $_POST['trip_notes'] ) );
    }

    if ( isset( $_POST['trip_description'] ) ) {
        update_post_meta($post_id, '_trip_description', wp_kses_post( $_POST['trip_description'] ));
    }

    if ( isset( $_POST['trip_start_date'] ) ) {
        update_post_meta( $post_id, '_trip_start_date', sanitize_text_field( $_POST['trip_start_date'] ) );
    }
    if ( isset( $_POST['trip_starting_address'] ) ) {
        update_post_meta( $post_id, '_trip_starting_address', sanitize_text_field( $_POST['trip_starting_address'] ) );
        update_post_meta( $post_id, '_trip_starting_address_lat', sanitize_text_field( $_POST['trip_starting_address_lat'] ) );
        update_post_meta( $post_id, '_trip_starting_address_lang', sanitize_text_field( $_POST['trip_starting_address_lang'] ) );
    }
    if ( isset( $_POST['trip_customer_id'] )) {
        if ( $_POST['trip_customer_id'] == 0 ) {
            $customer_id = pos_add_new_customer($_POST['trip_customer_first_name'], $_POST['trip_customer_last_name'], $_POST['trip_email'], $_POST['trip_phone'], $_POST['trip_password']);
            update_post_meta( $post_id, '_trip_customer_id', $customer_id );
        }else{
            update_post_meta( $post_id, '_trip_customer_id', sanitize_text_field( $_POST['trip_customer_id'] ) );
        }
    }
    if ( isset( $_POST['trip_customer_first_name'] ) ) {
        update_post_meta( $post_id, '_trip_customer_first_name', sanitize_text_field( $_POST['trip_customer_first_name'] ) );
    }   
    if ( isset( $_POST['trip_customer_last_name'] ) ) {
        update_post_meta( $post_id, '_trip_customer_last_name', sanitize_text_field( $_POST['trip_customer_last_name'] ) );
    }
    if ( isset( $_POST['trip_phone'] ) ) {
        update_post_meta( $post_id, '_trip_phone', sanitize_text_field( $_POST['trip_phone'] ) );
    }
    if ( isset( $_POST['trip_email'] ) ) {
        update_post_meta( $post_id, '_trip_email', sanitize_text_field( $_POST['trip_email'] ) );
    }
    if ( isset( $_POST['trip_password'] ) ) {
        update_post_meta( $post_id, '_trip_password', sanitize_text_field( $_POST['trip_password'] ) );
    } 
    $day = get_post_meta($post_id, '_trip_day_count', true);
    if ( !$day) {
        update_post_meta( $post_id, '_trip_day_count', 0 );
    }
    if ( isset( $_POST['trip_ending_address'] ) ) {
        update_post_meta( $post_id, '_trip_ending_address', sanitize_text_field( $_POST['trip_ending_address'] ) );
        update_post_meta( $post_id, '_trip_ending_address_lat', sanitize_text_field( $_POST['trip_ending_address_lat'] ) );
        update_post_meta( $post_id, '_trip_ending_address_lang', sanitize_text_field( $_POST['trip_ending_address_lang'] ) );
     
        $start_lat = sanitize_text_field($_POST['trip_starting_address_lat']);
        $start_lng = sanitize_text_field($_POST['trip_starting_address_lang']);
        $end_lat = sanitize_text_field($_POST['trip_ending_address_lat']);
        $end_lng = sanitize_text_field($_POST['trip_ending_address_lang']);
    
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$start_lat,$start_lng&destination=$end_lat,$end_lng&units=imperial&key=".esc_attr(get_option('google_api_key'));
    
        $response = wp_remote_get($url);
    
        if (is_array($response) && !is_wp_error($response)) {
            $directions_data = json_decode(wp_remote_retrieve_body($response), true);
            update_post_meta( $post_id, '_trip_direction_data', $directions_data );
        }
        
    }
}
add_action( 'save_post', 'save_trip_details_metabox' );




function display_trip_details_on_edit( $post ) {
    $trip_location = get_post_meta( $post->ID, '_trip_location', true );
    $trip_duration = get_post_meta( $post->ID, '_trip_duration', true );

    echo '<p><strong>' . __( 'Location:', 'your-plugin-textdomain' ) . '</strong> ' . esc_html( $trip_location ) . '</p>';
    echo '<p><strong>' . __( 'Duration:', 'your-plugin-textdomain' ) . '</strong> ' . esc_html( $trip_duration ) . '</p>';
}
add_action( 'trip_edit_form_after_title', 'display_trip_details_on_edit' );

function add_day_metabox() {
    global $post;
    if ($post && $post->post_type === 'trip' && $post->post_status !== 'auto-draft') {    add_meta_box(
        'add-day',
        __( 'Add Stay', 'your-plugin-textdomain' ),
        'render_add_day_metabox',
        'trip',
        'side',
        'default'
    );
  }
}
add_action( 'add_meta_boxes', 'add_day_metabox' );

function render_add_day_metabox( $post ) {
    $trip_id = $post->ID;
    $day_count = get_post_meta($trip_id, '_trip_day_count', true);
    
    // Register and enqueue Sortable.js library
    wp_register_script(
        'sortable-js', 
        'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', 
        array(), 
        '1.15.0', 
        true
    );
    wp_enqueue_script('sortable-js');
    
    // Add custom CSS
    ?>
    <style>
        .day-wraps .day-item {
            background: #f8f8f8;
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            cursor: move;
            border-radius: 3px;
        }
        .day-wraps .day-item:hover {
            background: #f0f0f0;
        }
        .day-wraps .day-item a {
            display: block;
            text-decoration: none;
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen {
            background: #e8e8e8 !important;
        }
        .sortable-drag {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .order-updated-message {
            color: green;
            display: none;
            margin: 10px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .add-day-button, .save-order-button {
            display: block;
            margin-top: 10px;
            padding: 8px;
            text-decoration: none;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }
        .save-order-button {
            background-color: #e0e0e0;
            color: #888;
            cursor: not-allowed;
            pointer-events: none;
        }
        .save-order-button.active {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            pointer-events: auto;
        }
    </style>
    
    <div class="day-wraps" id="stay-sortable">
        <?php
        // Collect all stays with their original route numbers
        $stays = array();
        
        // Get all meta keys that start with _route_title_
        global $wpdb;
        $meta_keys = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key, meta_value FROM $wpdb->postmeta 
                WHERE post_id = %d AND meta_key LIKE '_route_title_%%'
                ORDER BY meta_key ASC",
                $trip_id
            )
        );
        
        // Build the stays array with original route numbers
        foreach ($meta_keys as $meta) {
            $route_number = str_replace('_route_title_', '', $meta->meta_key);
            $route_value = get_post_meta($trip_id, "_route_" . $route_number, true);
            
            if ($route_value) {
                $stays[] = array(
                    'route_num' => $route_number,
                    'title' => $meta->meta_value,
                    'route' => $route_value
                );
            }
        }
        
        // Sort stays by a display order meta if it exists
        $display_order = get_post_meta($trip_id, '_route_display_order', true);
        if (!empty($display_order)) {
            $display_order = explode(',', $display_order);
            $sorted_stays = array();
            
            // Reorder stays according to display_order
            foreach ($display_order as $route_num) {
                foreach ($stays as $stay) {
                    if ($stay['route_num'] == $route_num) {
                        $sorted_stays[] = $stay;
                        break;
                    }
                }
            }
            
            // Add any stays that weren't in the display order
            foreach ($stays as $stay) {
                if (!in_array($stay['route_num'], $display_order)) {
                    $sorted_stays[] = $stay;
                }
            }
            
            $stays = $sorted_stays;
        }
        
        // Output stays
        foreach ($stays as $stay) {
            echo '<div class="day-item" data-route-num="' . $stay['route_num'] . '">
                <a class="day-button" href="admin.php?page=trip-day&tab=edit_day&trip=' . $trip_id .'&day=' . $stay['route_num'] .'">' . $stay['title'] . '</a>
            </div>';
        }
        ?>
    </div>
    
    <a class="add-day-button" href="admin.php?page=trip-day&tab=add_day&trip=<?php echo $trip_id; ?>">+ Add a Stay</a>
    
    <button class="save-order-button" id="save-order-btn" disabled>Save New Order</button>
    
    <div class="order-updated-message">Order updated successfully!</div>
    
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var tripId = <?php echo $trip_id; ?>;
        var nonce = "<?php echo wp_create_nonce('update_stay_order_nonce'); ?>";
        var saveOrderBtn = document.getElementById('save-order-btn');
        var originalOrder = [];
        
        // Initialize Sortable
        var el = document.getElementById('stay-sortable');
        var sortable = Sortable.create(el, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onStart: function() {
                // Store original order when dragging starts
                originalOrder = Array.from(el.querySelectorAll('.day-item')).map(function(item) {
                    return item.getAttribute('data-route-num');
                });
            },
            onEnd: function() {
                // Get the new order of route numbers
                var currentOrder = Array.from(el.querySelectorAll('.day-item')).map(function(item) {
                    return item.getAttribute('data-route-num');
                });
                
                // Compare current order with original order
                var orderChanged = JSON.stringify(originalOrder) !== JSON.stringify(currentOrder);
                
                // Enable/disable save button based on order change
                if (orderChanged) {
                    saveOrderBtn.classList.add('active');
                    saveOrderBtn.disabled = false;
                } else {
                    saveOrderBtn.classList.remove('active');
                    saveOrderBtn.disabled = true;
                }
            }
        });
        
        // Save order button click event
        saveOrderBtn.addEventListener('click', function() {
            // Show confirmation dialog
            var confirmSave = confirm('It will remove your Stops.\nAre you sure you want to save the new order of stays?');
            
            if (confirmSave) {
                // Get the new order of route numbers
                var items = Array.from(el.querySelectorAll('.day-item'));
                var order = items.map(function(item) {
                    return item.getAttribute('data-route-num');
                });
                
                // Send AJAX request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxurl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            var msg = document.querySelector('.order-updated-message');
                            msg.style.display = 'block';
                            setTimeout(function() {
                                msg.style.display = 'none';
                            }, 1500);
                            
                            // Disable save button and reset state
                            saveOrderBtn.classList.remove('active');
                            saveOrderBtn.disabled = true;
                            
                            // Update original order
                            originalOrder = order;
                        } else {
                            alert('Failed to update stay order. Please try again.');
                        }
                    }
                };
                
                var data = 'action=update_stay_order' + 
                           '&trip_id=' + tripId + 
                           '&order=' + encodeURIComponent(JSON.stringify(order)) + 
                           '&security=' + nonce;
                           
                xhr.send(data);
            }
        });
    });
    </script>
    <?php
}

// Handle the AJAX request to update stay order
function update_stay_order() {
    // Check security nonce
    check_ajax_referer('update_stay_order_nonce', 'security');
    
    // Get and validate trip ID
    $trip_id = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;
    if (!$trip_id) {
        wp_send_json_error('Invalid trip ID');
    }
    
    // Get and validate order array
    $order = isset($_POST['order']) ? json_decode(stripslashes($_POST['order']), true) : array();
    if (!is_array($order) || empty($order)) {
        wp_send_json_error('Invalid order data');
    }
    
    // Get current stays
    global $wpdb;
    $existing_stays = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT meta_key, meta_value FROM $wpdb->postmeta 
            WHERE post_id = %d AND meta_key LIKE '_route_title_%%'
            ORDER BY meta_key ASC",
            $trip_id
        )
    );
    
    // Find the max route number of existing stays
    $max_route_num = 0;
    foreach ($existing_stays as $stay) {
        $route_num = intval(str_replace('_route_title_', '', $stay->meta_key));
        $max_route_num = max($max_route_num, $route_num);
    }
    
    // If the submitted order doesn't match the number of existing stays,
    // it means a new stay has been added
    if (count($order) > count($existing_stays)) {
        // Find the route number of the new stay
        $new_stay_route_num = count($existing_stays) + 1;
        
        // Always append the new stay to the end
        $order = array_merge(
            array_diff($order, [$new_stay_route_num]),  // Remove any existing reference to new stay
            [$new_stay_route_num]  // Add new stay at the end
        );
    }
    
    // Save the display order as a comma-separated string
    update_post_meta($trip_id, '_route_display_order', implode(',', $order));
    
    // Get trip starting and ending locations
    $trip_starting_address = get_post_meta($trip_id, '_trip_starting_address', true);
    $trip_starting_lat = get_post_meta($trip_id, '_trip_starting_address_lat', true);
    $trip_starting_lng = get_post_meta($trip_id, '_trip_starting_address_lang', true);
    
    $trip_ending_address = get_post_meta($trip_id, '_trip_ending_address', true);
    $trip_ending_lat = get_post_meta($trip_id, '_trip_ending_address_lat', true);
    $trip_ending_lng = get_post_meta($trip_id, '_trip_ending_address_lang', true);
    
    // Process stays in the new order and update each one
    $current_start_address = $trip_starting_address;
    $current_start_lat = $trip_starting_lat;
    $current_start_lng = $trip_starting_lng;
    
    // First pass - update all stays with new starting/ending points
    for ($i = 0; $i < count($order); $i++) {
        $route_num = $order[$i];
        
        // Update this stay's starting point to be the previous stay's ending point
        // (or the trip's starting point for the first stay)
        update_post_meta($trip_id, "_route_day_start_address_" . $route_num, $current_start_address);
        update_post_meta($trip_id, "_route_day_start_address_lat_" . $route_num, $current_start_lat);
        update_post_meta($trip_id, "_route_day_start_address_lng_" . $route_num, $current_start_lng);
        
        // Get this stay's ending point for the next iteration
        $stay_end_address = get_post_meta($trip_id, "_route_day_end_address_" . $route_num, true);
        $stay_end_lat = get_post_meta($trip_id, "_route_day_end_address_lat_" . $route_num, true);
        $stay_end_lng = get_post_meta($trip_id, "_route_day_end_address_lng_" . $route_num, true);
        
        // Check if this is the last stay and if it's marked as final day
        $is_last = ($i == count($order) - 1);
        $is_final_day = get_post_meta($trip_id, "_is_final_day_" . $route_num, true);
        
        if ($is_last && $is_final_day == 'on') {
            // If this is the last stay and it's marked as final day, 
            // set its ending point to the trip's ending point
            $stay_end_address = $trip_ending_address;
            $stay_end_lat = $trip_ending_lat;
            $stay_end_lng = $trip_ending_lng;
            
            // Update the ending point for this stay
            update_post_meta($trip_id, "_route_day_end_address_" . $route_num, $stay_end_address);
            update_post_meta($trip_id, "_route_day_end_address_lat_" . $route_num, $stay_end_lat);
            update_post_meta($trip_id, "_route_day_end_address_lng_" . $route_num, $stay_end_lng);
        }

         // Remove stops for this stay

        $stops_meta_key = "_selected_destinations_" . $route_num;
        update_post_meta($trip_id, $stops_meta_key, array());
        
        // Set up for the next iteration - the next stay will start where this one ends
        $current_start_address = $stay_end_address;
        $current_start_lat = $stay_end_lat;
        $current_start_lng = $stay_end_lng;
    }
    
    // Second pass - update all routes with new directions based on updated points
    for ($i = 0; $i < count($order); $i++) {
        $route_num = $order[$i];
        
        // Get updated start and end coordinates for this stay
        $start_lat = get_post_meta($trip_id, "_route_day_start_address_lat_" . $route_num, true);
        $start_lng = get_post_meta($trip_id, "_route_day_start_address_lng_" . $route_num, true);
        $end_lat = get_post_meta($trip_id, "_route_day_end_address_lat_" . $route_num, true);
        $end_lng = get_post_meta($trip_id, "_route_day_end_address_lng_" . $route_num, true);
        
        // Update route directions via Google Maps API
        if (!empty($end_lat) && !empty($end_lng) && !empty($start_lat) && !empty($start_lng)) {
            $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$start_lat,$start_lng&destination=$end_lat,$end_lng&units=imperial&key=" . esc_attr(get_option('google_api_key'));
            $directions_data = wp_remote_get($url);
            if (!is_wp_error($directions_data)) {
                $response_body = wp_remote_retrieve_body($directions_data);
                $data = json_decode($response_body);
                
                if ($data && isset($data->routes[0]->legs)) {
                    $route_data = $data->routes[0]->legs;
                    
                    // Update the route data
                    update_post_meta($trip_id, "_route_" . $route_num, $response_body);
                    update_post_meta($trip_id, "_end_route_" . $route_num, $route_data);
                    
                    // Update travel time
                    $duration_seconds = $data->routes[0]->legs[0]->duration->value;
                    $hours = floor($duration_seconds / 3600);
                    $minutes = floor(($duration_seconds % 3600) / 60);
                    $end_travel_time = sprintf('%d:%02d', $hours, $minutes);
                    update_post_meta($trip_id, "_end_route_time_" . $route_num, $end_travel_time);
                }
            }
        }
    }
    
    wp_send_json_success(array(
        'message' => 'Stay order updated successfully',
        'order' => $order
    ));
}



add_action('wp_ajax_update_stay_order', 'update_stay_order');

function add_custom_trip_columns($columns) {

    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => $columns['title'],
        'trip_customer_name' => __('Customer Name', 'your-plugin-textdomain'),
        'trip_start_date' => __('Start Date', 'your-plugin-textdomain'),
        'trip_phone' => __('Phone', 'your-plugin-textdomain'),
        'trip_email' => __('Email', 'your-plugin-textdomain'),
        // 'trip_button' => __('Action', 'your-plugin-textdomain'),
    );
    return $new_columns;
}
add_filter('manage_edit-trip_columns', 'add_custom_trip_columns');


function populate_custom_trip_columns($column, $post_id) {
     switch ($column) {
        case 'trip_location':
            echo get_post_meta($post_id, '_trip_location', true);
            break;
        
        case 'trip_start_date':
            echo get_post_meta($post_id, '_trip_start_date', true);
            break;
        
        case 'trip_customer_name':
            $customer_id = get_post_meta($post_id, '_trip_customer_id', true);
            $user = get_user_by('id', $customer_id);
            if($user){ echo $user->display_name; }
            break;

        case 'trip_phone':
            echo get_post_meta($post_id, '_trip_phone', true);
            break;
    
        case 'trip_email':
            echo get_post_meta($post_id, '_trip_email', true);
            break;   
    }
}
add_action('manage_trip_posts_custom_column', 'populate_custom_trip_columns', 10, 2);



function make_custom_trip_columns_sortable($sortable_columns) {
    $sortable_columns['trip_location'] = 'trip_location';
    $sortable_columns['trip_start_date'] = 'trip_start_date';
    $sortable_columns['trip_customer_name'] = 'trip_customer_name';

    return $sortable_columns;
}
add_filter('manage_edit-trip_sortable_columns', 'make_custom_trip_columns_sortable');


// get all users
function pos_get_all_users($selected_id = null) {
    $args = array(
        'role'    => 'traveler',
        'orderby' => 'id',
        'order'   => 'DESC'
    );
    $users = get_users($args);

    echo '<label for="user_id">' . __( 'Customer : ', 'your-plugin-textdomain' ) . '</label>';
    echo '<select class="full-width" name="trip_customer_id" id="trip_customer_id">';
    echo '<option>select customer</option>';
    echo '<option value="0">Add New Customer</option>';
    foreach ($users as $user) {
        echo '<option value="' . esc_attr($user->ID) . '" ';
        selected($user->ID, $selected_id);
        echo '>' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</option>';
    }
    echo '</select>';
}

function get_customer_data() {
    $customer_id = $_POST['customer_id'];
    if ($customer_id > 0) {
        $user_data = get_userdata($customer_id);
        $customer_data = array(
            'first_name' => get_user_meta($customer_id, 'first_name', true),
            'last_name' => get_user_meta($customer_id, 'last_name', true),
            'email' => $user_data->user_email,
            'phone' => get_user_meta($customer_id, 'phone_number', true),
        );

        echo json_encode($customer_data);
    }else{
        echo json_encode(array());
    }
    wp_die(); 
}

add_action('wp_ajax_get_customer_data', 'get_customer_data');

function pos_add_new_customer($first_name, $last_name, $email, $phone, $password) {
    $username = sanitize_user($first_name);

    $user_id = wp_insert_user(array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass'  => $password,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'role'       => 'traveler',
    ));

    if (is_wp_error($user_id)) {
        return false;
    }

    add_user_meta($user_id, 'phone_number', $phone);

    return $user_id;
}
