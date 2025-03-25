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
    }

    if(isset($_GET['file_delete'])){
        $ids = $_GET['file_delete'];
        $file_data = get_post_meta($post_id, '_upload_document', true);
        $file_data_array = explode(',', $file_data);
        $file_data_array = array_diff($file_data_array, [$ids]);
        $new_upload_document = implode(',', $file_data_array);
        update_post_meta($post_id, '_upload_document', $new_upload_document);
        echo '<script>window.location.href = "' . admin_url( 'admin.php?page=trip-day&tab=day_overview&trip=' . $post_id ) . '";</script>';

    }
    
    $trip_title = get_the_title($post_id);
    $trip_notes = get_post_meta( $post_id, '_trip_notes', true );
    $trip_start_date = get_post_meta( $post_id, '_trip_start_date', true );
    $trip_phone = get_post_meta( $post_id, '_trip_phone', true );
    $trip_email = get_post_meta( $post_id, '_trip_email', true );
    $upload_document = get_post_meta($post_id, '_upload_document', true);


    echo '<div class="day-wrap">';
    echo '<h2>'. $trip_title .'</h2>';
    echo '<h5>Notes : '. $trip_notes .'</h5>';
    echo '<h5>Starting Day : '. date($trip_start_date) .'</h5>';
    echo '<h5>'. $trip_phone .'</h5>';
    echo '<h5>'. $trip_email .'</h5>';

    echo '<table class="widefat">
        <thead>
            <tr>
                <th colspan="6">Overview</th>
            </tr>
        </thead>
        <tbody>';
    $day = get_post_meta($post_id, '_trip_day_count', true);
        for ($i=1; $i <= $day; $i++) { 

        $current_route = "_route_" . $i;
        $current_title = "_route_title_" . $i;    
        $current_value = get_post_meta($post_id, $current_route, true);
        
        if ($current_value) {
            $current_title = get_post_meta($post_id, $current_title, true);
            echo '<tr>
                    <td><b>'. $current_title .'</b></td>
                    <td class="wrap-right">
                        <a class="button" href="admin.php?page=trip-day&tab=edit_day&trip=' . $post_id .'&day=' . $i .'">Edit</a>&nbsp;
                        <a class="button" href="admin.php?page=trip-day&tab=view_day&trip=' . $post_id .'&day=' . $i .'">View</a>&nbsp;
                        <a class="mt-1" href="#" ><span class="dashicons dashicons-admin-page"></span></a>&nbsp;
                        <a class="mt-1" href="admin.php?page=trip-day&tab=day_overview&trip=' . $post_id .'&day_delete=' . $i .'" ><span class="dashicons dashicons-trash"></span></a>&nbsp;
                        <a class="mt-1" href="#" ><span class="dashicons dashicons-menu-alt3"></span></a>&nbsp;
                    </td>
                </tr>';
        } else {
            continue;
        }
    }
   
       echo '</tbody>
    </table>';

    echo '</br></br><a class="preview button" href="admin.php?page=trip-day&tab=add_day&trip=' . $post_id .'">Add Day</a></br></br><hr>';
    render_document_upload_meta_box($post_id);