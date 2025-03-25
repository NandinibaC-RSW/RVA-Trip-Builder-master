<?php 

// Define custom post type
function custom_destination_post_type() {
    $labels = array(
        'name'               => _x( 'Destination', 'post type general name', 'your-plugin-textdomain' ),
        'singular_name'      => _x( 'Destination', 'post type singular name', 'your-plugin-textdomain' ),
        'menu_name'          => _x( 'Destination Library', 'admin menu', 'your-plugin-textdomain' ),
        'add_new'            => __( 'Add Destination', 'your-plugin-textdomain' ),
        'add_new_item'       => __( 'Add New Destination', 'your-plugin-textdomain' ),
        'edit_item'          => __( 'Edit Destination', 'your-plugin-textdomain' ),
        'new_item'           => __( 'New Destination', 'your-plugin-textdomain' ),
        'view_item'          => __( 'View Destination', 'your-plugin-textdomain' ),
        'search_items'       => __( 'Search Destination', 'your-plugin-textdomain' ),
        'not_found'          => __( 'No destination found', 'your-plugin-textdomain' ),
        'not_found_in_trash' => __( 'No destination found in Trash', 'your-plugin-textdomain' ),

    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_position'      => 20,
        'rewrite'            => array( 'slug' => 'destination' ),
        'supports'           => array( 'title' ),
        'show_in_menu'       => 'edit.php?post_type=trip'
    );

    register_post_type( 'destination', $args );
}
add_action( 'init', 'custom_destination_post_type' );

function create_destination_taxonomy() {
    $labels = array(
        'name' => 'Destination Category',
        'singular_name' => 'Destination Category',
        'menu_name' => 'Destination Category',
        'all_items' => 'All Items',
        'edit_item' => 'Edit Item',
        'view_item' => 'View Item',
        'update_item' => 'Update Item',
        'add_new_item' => 'Add New Item',
        'new_item_name' => 'New Item Name',
        'search_items' => 'Search Items',
        'popular_items' => 'Popular Items',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'destination_category'),
    );

    register_taxonomy('destination_category', array('destination'), $args);
}
add_action('init', 'create_destination_taxonomy');


// Add metabox
function destination_details_metabox() {
    add_meta_box(
        'destination-details',
        __( 'Destination Details', 'your-plugin-textdomain' ),
        'render_destination_details_metabox',
        'destination',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'destination_details_metabox' );


function render_destination_details_metabox( $post ) {
       // Retrieve saved values
    $destination_address = get_post_meta( $post->ID, '_destination_address', true );
    $destination_lat = get_post_meta( $post->ID, '_destination_lat', true );
    $destination_lang = get_post_meta( $post->ID, '_destination_lang', true );
    $check_in_time = get_post_meta( $post->ID, '_check_in_time', true );
    $destination_description = get_post_meta( $post->ID, '_destination_description', true );
    $dest_phone = get_post_meta( $post->ID, '_dest_phone', true );
    $dest_website = get_post_meta( $post->ID, '_dest_website', true );

    // Output the fields

    echo '<label for="destination-address">' . __( 'Address:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="text" id="get_address" name="destination_address" value="' . esc_attr( $destination_address ) . '" class="full-width" />
          <input type="hidden" id="address_lat" name="destination_lat" value="' . esc_attr( $destination_lat ) . '">
          <input type="hidden" id="address_lng" name="destination_lang" value="' . esc_attr( $destination_lang ) . '">  <br /><br/>';

    echo '<label for="destination-checkin">' . __( 'Check-in Hours & Minutes:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="time" id="destination-checkin" name="check_in_time" value="' . esc_attr( $check_in_time ) . '" class="full-width" /><br /><br/>';

    echo '<label for="destination-phone">' . __( 'Phone:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="number" id="destination-phone" name="dest_phone" value="' . esc_attr( $dest_phone ) . '" class="full-width" /><br /><br/>';

    echo '<label for="destination-website">' . __( 'Website:', 'your-plugin-textdomain' ) . '</label>';
    echo '<input type="url" id="destination-website" name="dest_website" value="' . esc_attr( $dest_website ) . '" class="full-width" /><br /><br/>';

    echo '<label for="destination-description">' . __( 'Short Description:', 'your-plugin-textdomain' ) . '</label>';
    echo '<textarea id="destination-description" name="destination_description" class="full-width">' . esc_textarea( $destination_description ) . '</textarea><br /><br/>';

    $image_url = esc_attr( get_post_meta( $post->ID, '_destination_image', true ) );
    echo '<label for="destination-image">' . __( 'Image:', 'your-plugin-textdomain' ) . '</label>';
    echo '<br/><br/><div id="image-preview">';
    if (!empty($image_url)) {
        echo '<img src="' . $image_url . '" style="max-width: 200px; height: auto;" />';
    }
    echo '</div>';
    echo '<input type="hidden" id="destination-image" name="destination_image" value="' . $image_url . '" class="full-width" />
        <input type="button" id="upload-image-button" class="button" value="Upload Image" /><hr>';

    render_destination_document_upload_meta_box($post->ID);
}



function save_destination_details_metabox( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if ( isset( $_POST['destination_address'] ) ) {
        update_post_meta( $post_id, '_destination_address', sanitize_text_field( $_POST['destination_address'] ) );
        update_post_meta( $post_id, '_destination_lat', sanitize_text_field( $_POST['destination_lat'] ) );
        update_post_meta( $post_id, '_destination_lang', sanitize_text_field( $_POST['destination_lang'] ) );
    }
    if ( isset( $_POST['check_in_time'] ) ) {
        update_post_meta( $post_id, '_check_in_time', sanitize_text_field( $_POST['check_in_time'] ) );
    } 
    if ( isset( $_POST['dest_phone'] ) ) {
        update_post_meta( $post_id, '_dest_phone', sanitize_text_field( $_POST['dest_phone'] ) );
    } 
    if ( isset( $_POST['dest_website'] ) ) {
        update_post_meta( $post_id, '_dest_website', sanitize_text_field( $_POST['dest_website'] ) );
    }
    if ( isset( $_POST['destination_description'] ) ) {
        update_post_meta( $post_id, '_destination_description', sanitize_text_field( $_POST['destination_description'] ) );
    }
    if ( isset( $_POST['destination_image'] ) ) {
        update_post_meta( $post_id, '_destination_image', sanitize_text_field( $_POST['destination_image'] ) );
    }
   
}
add_action( 'save_post', 'save_destination_details_metabox' );


function add_custom_destination_columns($columns) {

    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => $columns['title'],
        'destination_category' => __('Category', 'your-plugin-textdomain'),
        'check_in_time' => __('Check In Time', 'your-plugin-textdomain'),
        'destination_address' => __('Address', 'your-plugin-textdomain'),
    );
    return $new_columns;
}
add_filter('manage_edit-destination_columns', 'add_custom_destination_columns');


function populate_custom_destination_columns($column, $post_id) {
     switch ($column) {
        case 'destination_address':
            echo get_post_meta($post_id, '_destination_address', true);
            break;
        
        case 'destination_category':
                $term = wp_get_post_terms($post_id, 'destination_category');
                if (!is_wp_error($term)) {
                    echo $term[0]->name;
                }
            
            break;
        
        case 'check_in_time':
            echo get_post_meta($post_id, '_check_in_time', true);
            break;
        
        case 'destination_description':
            echo get_post_meta($post_id, '_destination_description', true);
            break;
    }
}
add_action('manage_destination_posts_custom_column', 'populate_custom_destination_columns', 10, 2);


function make_custom_destination_columns_sortable($sortable_columns) {
    $sortable_columns['destination_category'] = 'destination_category';
    $sortable_columns['check_in_time'] = 'check_in_time';
    // Add other columns as needed

    return $sortable_columns;
}
add_filter('manage_edit-destination_sortable_columns', 'make_custom_destination_columns_sortable');