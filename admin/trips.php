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

    echo '<div class="day-wraps">';

    for ($i = 1; $i <= $day_count; $i++) { 
        $current_route = "_route_" . $i;
        $current_title = "_route_title_" . $i;    
        $current_value = get_post_meta($trip_id, $current_route, true);
        
        if ($current_value) {
            $current_title = get_post_meta($trip_id, $current_title, true);
            echo '<p><a class="day-button" href="admin.php?page=trip-day&tab=edit_day&trip=' . $trip_id .'&day=' . $i .'"> '. $current_title .'</a></p>';
        }
    }

    // Display Add Day Button
    echo '<a class="add-day-button" href="admin.php?page=trip-day&tab=add_day&trip=' . $trip_id .'">+ Add a Stay</a></br></br>';

    echo '</div>';
}




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
