<?php

function add_trip_day_page() {
    add_menu_page(
        'Trip Day Editor',       // Page Title
        'Trip Day',       // Menu Title
        'manage_options',    // Capability required to access
        'trip-day',       // Menu Slug
        'trip_day_page_content' // Callback function to display content
    );
}

add_action('admin_menu', 'add_trip_day_page');

function remove_trip_day_page() {
    remove_menu_page('trip-day');
}

add_action('admin_menu', 'remove_trip_day_page');

function trip_day_page_content() {

    $tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'day_overview' );

        if( $tab == 'day_overview' ):

        include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/trip-day/day_overview.php';

        elseif( $tab == 'add_day' ):

        include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/trip-day/add_day.php';

        elseif( $tab == 'edit_day' ):

        include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/trip-day/edit_day.php';

        elseif( $tab == 'view_day' ):

        include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/trip-day/view_day.php';

        endif;

}
