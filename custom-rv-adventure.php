<?php
 /*
 * Plugin Name:       Custom RV Adventure
 * Plugin URI:        https://rvadventure-usa.com
 * Description:       Basic RV Adventure application for your build trips
 * Version:           0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            RV Adventure USA
 * Author URI:        https://rvadventure-usa.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       woocommerce-extension
 * Domain Path:       /languages
 */

function admin_script(){

    
    wp_enqueue_script( 'custom-rv-js4','https://code.jquery.com/jquery-2.2.4.min.js',NULL,true );
    wp_enqueue_script('custom-rv-autocomplete','https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', NULL, true);
    wp_enqueue_script('custom-fbfinder-js3', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr(get_option('google_api_key')) . '&units=imperial&callback=initMap&libraries=places&v=weekly', NULL, true, true);
    wp_register_style('current-template-autocomplete','https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
    wp_enqueue_media();

    wp_enqueue_style('custom-style', plugin_dir_url(__FILE__).'/admin/assets/css/style.css' );
    wp_enqueue_script('custom-rv-js', plugin_dir_url(__FILE__).'/admin/assets/js/custom.js');
    wp_enqueue_script('custom-rv-map-js', plugin_dir_url(__FILE__).'/admin/assets/js/map.js');
    wp_localize_script('custom-rv-js', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));


}
add_action( 'admin_enqueue_scripts', 'admin_script' );

function enqueue_select2_scripts() {
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_select2_scripts');

function front_script(){

    wp_enqueue_script( 'custom-rv-js4','https://code.jquery.com/jquery-2.2.4.min.js',NULL,true );
    wp_enqueue_script('custom-rv-autocomplete','https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', NULL, true);
    wp_enqueue_script('custom-fbfinder-js3','https://maps.googleapis.com/maps/api/js?key='.esc_attr(get_option('google_api_key')).'&units=imperial&callback=initMap&libraries=places&v=weekly',NULL, true);
    wp_enqueue_script('custom-rv-autocomplete','https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', NULL, true);
    wp_enqueue_script('custom-rv-jq','https://code.jquery.com/jquery-3.7.1.js', NULL, true);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
    wp_enqueue_media();

    wp_enqueue_style('custom-front-style', plugin_dir_url(__FILE__).'/assets/css/style.css' );
    wp_enqueue_script('custom-rv-front-js', plugin_dir_url(__FILE__).'/assets/js/custom.js');

}
add_action( 'wp_enqueue_scripts', 'front_script' );


require 'admin/trips.php';
require 'admin/destination.php';
require 'admin/trip-day-editor.php';
require 'admin/settings.php';
require 'trip-builder-home.php';
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

add_action('wp_ajax_update_route_data', 'update_route_data_callback');

function update_route_data_callback() {
    $post_id = $_POST['postId'];
    $day = $_POST['day'];
    $direction_data = $_POST['data'];
    $dest_id = $_POST['id'];
    $update_result = update_post_meta($post_id, '_direction_data_'.$post_id.'_'.$day, $direction_data);
    if ($update_result) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to update data.'));
    }
    wp_die();
}

function add_traveler_role() {
    add_role(
        'traveler',
        __('Traveler'),
        array(
            'read' => true,
        )
    );
}
add_action('init', 'add_traveler_role');

function render_document_upload_meta_box($post, $day = null) {
    if($day){
        echo '<br><label for="trip_ending_address">' . __( 'Uploads : ', 'your-plugin-textdomain' ) . '</label>';
        $upload_name = '_upload_document_day_'.$day;     
        $upload_document = get_post_meta($post, $upload_name, true);
        wp_nonce_field(basename(__FILE__), 'document_upload_nonce');
        echo '<input type="button" id="upload_document_button" class="preview button" value="Upload Documents">';
        echo '<input type="hidden" name="upload_document" id="upload_document" value="'.$upload_document.'"></br></br>';
        echo '<input type="hidden" name="post_id" id="post_id" value="'.$post.'">';
        echo '<input type="hidden" name="days" id="days" value="'.$day.'">';
        echo '<table class="widefat" id="document_preview"><tbody>';
        if($upload_document){
            $upload_document = explode(",", $upload_document);
            foreach ($upload_document as $value) {
                $file_path = get_attached_file($value);
                if($file_path) {
                    $file_name = basename($file_path);
                    $file_url = wp_get_attachment_url($value);
                    echo '<tr>
                        <td><b>'. $file_name .'</b></td>
                        <td class="wrap-right">
                        <a class="button view-button" href="' . $file_url .'">View</a>&nbsp;
                        <a class="mt-1" href="admin.php?page=trip-day&tab=edit_day&trip=' . $post .'&day='.$day.'&file_delete=' . $value .'" ><span class="dashicons dashicons-trash"></span></a>&nbsp;
                        </td>
                    </tr>';
                }
            }
        }
        echo '</tbody>
        </table>';    
        echo '<div class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div class="pdf-container"></div>
                </div>
              </div>';
    }else{
        echo '<br><label for="trip_ending_address">' . __( 'Uploads  : ', 'your-plugin-textdomain' ) . '</label>';
        $upload_document = get_post_meta($post, '_upload_document', true);
        wp_nonce_field(basename(__FILE__), 'document_upload_nonce');
        echo '<input type="button" id="upload_document_button" class="preview button" value="Upload Documents">';
        echo '<input type="hidden" name="upload_document" id="upload_document" value="'.$upload_document.'"></br></br>';
        echo '<input type="hidden" name="post_id" id="post_id" value="'.$post.'">';
        echo '<table class="widefat"><tbody>';
        if($upload_document){
            $upload_document = explode(",", $upload_document);
            foreach ($upload_document as $value) {
                $file_path = get_attached_file($value);
                if($file_path) {
                    $file_name = basename($file_path);
                    $file_url = wp_get_attachment_url($value);
                    echo '<tr>
                            <td><b>'. $file_name .'</b></td>
                            <td class="wrap-right">
                            <a class="button view-button" href="' . $file_url .'">View</a>&nbsp;
                            <a class="mt-1" href="admin.php?page=trip-day&tab=edit_day&trip=' . $post .'&day='.$day.'&file_delete=' . $value .'" ><span class="dashicons dashicons-trash"></span></a>&nbsp;
                            </td>
                        </tr>';
                }
            }
        }
        echo '</tbody>
        </table>';    
        echo '<div class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div class="pdf-container"></div>
                </div>
              </div>';
    }
}
function upload_documents_callback() {
    // More detailed error checking
    if (!isset($_POST['security'])) {
        wp_send_json_error(array('message' => 'Security check failed. Nonce verification failed.'));
        wp_die();
    }
    
    // Validate required parameters
    if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
        wp_send_json_error(array('message' => 'Missing post ID.'));
        wp_die();
    }
    
    $post_id = intval($_POST['post_id']);
    $day = isset($_POST['day']) ? intval($_POST['day']) : 0;
    $attachment_ids = isset($_POST['attachment_ids']) ? sanitize_text_field($_POST['attachment_ids']) : '';
    
    // Make sure user has permission to edit this post
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(array('message' => 'You do not have permission to edit this post.'));
        wp_die();
    }
    
    if ($post_id) {
        if ($day) {
            // Update day-specific documents
            $meta_key = '_upload_document_day_' . $day;
        } else {
            // Update general documents
            $meta_key = '_upload_document';
        }
        
        // Update post meta
        update_post_meta($post_id, $meta_key, $attachment_ids);
        
        wp_send_json_success(array('message' => 'Documents updated successfully'));
    } else {
        wp_send_json_error(array('message' => 'Invalid post ID'));
    }
    
    wp_die();
}
add_action('wp_ajax_upload_documents', 'upload_documents_callback');
add_action('wp_ajax_filter_destinations_by_category', 'filter_destinations_by_category_callback');

function filter_destinations_by_category_callback() {
    check_ajax_referer('filter-destinations', 'security');
    
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
    
    $args = array(
        'post_type' => 'destination',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    
    if ($category_id > 0) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'destination_category',
                'field' => 'term_id',
                'terms' => $category_id
            )
        );
    }
    
    $destinations = array();
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $destinations[] = array(
                'id' => $post_id,
                'title' => get_the_title(),
                'address' => get_post_meta($post_id, '_destination_address', true),
                'lat' => get_post_meta($post_id, '_destination_lat', true),
                'lng' => get_post_meta($post_id, '_destination_lang', true)
            );
        }
    }
    
    wp_reset_postdata();
    
    wp_send_json_success($destinations);
}
function render_destination_document_upload_meta_box($post) {
  echo '<br><label for="trip_ending_address">' . __( 'Destination Document Uploads  : ', 'your-plugin-textdomain' ) . '</label>';
  $upload_dest_document = get_post_meta($post, '_destination_document', true);
  wp_nonce_field(basename(__FILE__), 'document_upload_nonce');
  echo '<input type="button" id="upload_trip_document_button" class="preview button" value="Upload Documents">';
  echo '<input type="hidden" name="upload_dest_document" id="upload_trip_document" value="'.$upload_dest_document.'"></br></br>';
  echo '<input type="hidden" name="post_id" id="post_id" value="'.$post.'">';
  echo '<table class="widefat"><tbody>';
  if($upload_dest_document){
      $upload_trip_document = explode(",", $upload_dest_document);
      foreach ($upload_trip_document as $value) {
          $file_path = get_attached_file($value);
          if($file_path) {
              $file_name = basename($file_path);
              $file_url = wp_get_attachment_url($value);
              echo '<tr>
                      <td><b>'. $file_name .'</b></td>
                      <td class="wrap-right">
                      <a class="button view-button" href="' . $file_url .'">View</a>&nbsp;
                      <a class="mt-1" href="' . admin_url( 'admin.php?page=trip-edit&post=' . $post . '&file_delete=' . $value ) . '" ><span class="dashicons dashicons-trash"></span></a>&nbsp;
                      </td>
                  </tr>';
          }
      }
  }
  echo '</tbody></table>';    
  echo '<div class="modal">
          <div class="modal-content">
              <span class="close">&times;</span>
              <div class="pdf-container"></div>
          </div>
        </div>';
}

if(isset($_GET['file_delete'])){
  $ids = $_GET['file_delete'];
  $post_id = $_GET['trip'];
  $day = $_GET['day'];
  $name_field = '_upload_document_day_'.$day;
  $file_data = get_post_meta($post_id, $name_field, true);
  $file_data_array = explode(',', $file_data);
  $file_data_array = array_diff($file_data_array, [$ids]);
  $new_upload_document = implode(',', $file_data_array);
  update_post_meta($post_id, $name_field, $new_upload_document);
  echo '<script>window.location.href = "admin.php?page=trip-day&tab=edit_day&trip=' . $post_id .'&day='.$day.'";</script>';
}

// WordPress AJAX action hook
add_action('wp_ajax_change_destinations', 'change_destinations_callback');

function change_destinations_callback() {
     $dest_id = "";
    $destination_cat = intval($_POST['destination_cat']);
    $dest_id = $_POST['post_id'];
    $args = array(
    'post_type' => 'destination',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy' => 'destination_category', // Replace with the actual taxonomy name
            'field' => 'term_id',
            'terms' => $destination_cat,
        ),
    ),
);

    $selected_destination_name = "_selected_destinations_" . $day;
    $selected_destinations = get_post_meta($post_id, $selected_destination_name, true);
    $all_destinations = get_posts($args);
    echo " <option disabled value=''>Select Stop</option>";
    foreach ($all_destinations as $single_destination) {
        $option_value = $single_destination->ID;
        $selected_attribute = '';

        if (is_array($selected_destinations) && array_key_exists($option_value, $selected_destinations)) {
            $selected_attribute = 'selected';
            $destination_details = $selected_destinations[$option_value];
            $destination_address = $destination_details['address'];
            $destination_lat = $destination_details['lat'];
            $destination_lng = $destination_details['lng'];

            // Output the destination details as data attributes
            echo '<option value="' . esc_attr($option_value) . '" ' . $selected_attribute . ' data-address="' . esc_attr($destination_address) . '" data-lat="' . esc_attr($destination_lat) . '" data-lng="' . esc_attr($destination_lng) . '">' . esc_html($single_destination->post_title) . '</option>';
        } else {
            $destination_address = get_post_meta( $option_value, '_destination_address', true);
            $destination_lat =  get_post_meta( $option_value, '_destination_lat', true );
            $destination_lang =  get_post_meta( $option_value, '_destination_lang', true );
             if ($dest_id == $option_value) {
                  echo '<option value="' . esc_attr($option_value) . '" selected data-address="' . esc_attr($destination_address) . '" data-lat="' . esc_attr($destination_lat) . '" data-lng="' . esc_attr($destination_lang) . '">' . esc_html($single_destination->post_title) . '</option>';
            }else{
            echo '<option value="' . esc_attr($option_value) . '" ' . $selected_attribute . ' data-address="' . esc_attr($destination_address) . '" data-lat="' . esc_attr($destination_lat) . '" data-lng="' . esc_attr($destination_lang) . '">' . esc_html($single_destination->post_title) . '</option>';
            }
        }
    }

  wp_die(); 
}

// WordPress AJAX action hook
add_action('wp_ajax_upload_documents', 'update_post_meta_callback');

function update_post_meta_callback() {
    $post_id = intval($_POST['post_id']);
    $days = intval($_POST['day']);

    $attachment_ids = sanitize_text_field($_POST['attachment_ids']);
    $upload_name = '_upload_document_day_'.$days;     

    update_post_meta($post_id, $upload_name, $attachment_ids);

    wp_send_json_success('Post meta updated successfully.'); // Send a success response
    wp_die(); 
}

// WordPress AJAX action hook
add_action('wp_ajax_upload_trip_documents', 'update_post_meta_trip_callback');

function update_post_meta_trip_callback() {
    $post_id = intval($_POST['post_id']);
    $attachment_ids = sanitize_text_field($_POST['attachment_ids']);

    update_post_meta($post_id, '_destination_document', $attachment_ids);

    wp_send_json_success('Post meta updated successfully.'); // Send a success response
    wp_die(); 
}

// WordPress AJAX action hook
add_action('wp_ajax_add_destination', 'add_destination_callback');

function add_destination_callback() {

    $serializedFormData = $_POST['form_data'];
    parse_str($serializedFormData, $formData);

    $destination_title = $formData['destination_title'];    
    $destination_category = $formData['destination_category'];    
    $destination_address = $formData['destination_address'];    
    $destination_lat = $formData['destination_lat'];    
    $destination_lang = $formData['destination_lang'];    
    $check_in_time = $formData['check_in_time'];    
    $dest_phone = $formData['dest_phone'];    
    $dest_website = $formData['dest_website'];    
    $destination_description = $formData['destination_description'];    
    $destination_image = $formData['destination_image'];
    
    $my_post = array(
    'post_title'    => wp_strip_all_tags( $destination_title ),
    'post_content'  => $destination_description,
    'post_status'   => 'publish',
    'post_type'     => 'destination', 
    );
    $post_id = wp_insert_post( $my_post );
    
    if ( isset( $destination_category ) ) {
        wp_set_post_terms( $post_id, sanitize_text_field( $destination_category ), 'destination_category' );
    }
    if ( isset( $destination_address ) ) {
        update_post_meta( $post_id, '_destination_address', sanitize_text_field( $destination_address ) );
        update_post_meta( $post_id, '_destination_lat', sanitize_text_field( $destination_lat ) );
        update_post_meta( $post_id, '_destination_lang', sanitize_text_field( $destination_lang ) );
    }
    if ( isset( $check_in_time ) ) {
        update_post_meta( $post_id, '_check_in_time', sanitize_text_field( $check_in_time ) );
    } 
    if ( isset( $dest_phone ) ) {
        update_post_meta( $post_id, '_dest_phone', sanitize_text_field( $dest_phone ) );
    } 
    if ( isset( $dest_website ) ) {
        update_post_meta( $post_id, '_dest_website', sanitize_text_field( $dest_website ) );
    }
    if ( isset( $destination_description ) ) {
        update_post_meta( $post_id, '_destination_description', sanitize_text_field( $destination_description ) );
    }
    if ( isset( $destination_image ) ) {
        update_post_meta( $post_id, '_destination_image', sanitize_text_field( $destination_image ) );
    }
    wp_send_json_success(array('message' => 'Post meta updated successfully.', 'destination_category' => $destination_category, 'post_id' => $post_id, 'address' => $destination_address, 'destination_lat' => $destination_lat, 'destination_lang' => $destination_lang)); // Send a success response
    wp_die(); 
}
//hide admin bar
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
  if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
  }
}

//login redirect code if login/logout plugin not using

// function custom_login_redirect( $redirect_to, $request, $user ) {
//     // Get the current user's role
//     $user_role = $user->roles[0];
 
//     // Set the URL to redirect users to based on their role
//     if ( $user_role == 'subscriber' ) {
//         $redirect_to = get_site_url().'/trip-builder-home/';
//     } elseif ( $user_role == 'administrator' ) {
//         $redirect_to = get_site_url().'/wp-admin/';
//     }
 
//     return $redirect_to;
// }
// add_filter( 'login_redirect', 'custom_login_redirect', 10, 3 );

// Add phone number input field to user profile
function add_phone_number_field($user) {
    $phone_number = get_user_meta($user->ID, 'phone_number', true);
    ?>
    <h3>Additional Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone_number">Phone Number</label></th>
            <td>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr($phone_number); ?>" class="regular-text" /><br />
            </td>
        </tr>
    </table>
    <?php
}

// Save phone number input field data
function save_phone_number_field($user_id) {
    if (isset($_POST['phone_number']) && !empty($_POST['phone_number'])) {
        update_user_meta($user_id, 'phone_number', sanitize_text_field($_POST['phone_number']));
    } else {
        delete_user_meta($user_id, 'phone_number');
    }
}

// Add phone number input field to user profile in admin
add_action('show_user_profile', 'add_phone_number_field');
add_action('edit_user_profile', 'add_phone_number_field');
add_action('personal_options_update', 'save_phone_number_field');
add_action('edit_user_profile_update', 'save_phone_number_field');

// Add phone number field to the Add New User page
function add_phone_number_to_new_user_form($user_id) {
    add_action('user_new_form', 'add_phone_number_field');
}

function save_phone_number_on_user_creation($user_id) {
    if (isset($_POST['phone_number']) && !empty($_POST['phone_number'])) {
        update_user_meta($user_id, 'phone_number', sanitize_text_field($_POST['phone_number']));
    }
}

add_action('user_register', 'save_phone_number_on_user_creation', 10, 1);
add_action('admin_head', 'add_phone_number_to_new_user_form');
