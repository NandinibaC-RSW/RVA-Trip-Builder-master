<?php
function add_settings_page_submenu() {
    add_submenu_page(
        'edit.php?post_type=trip',  // Parent menu slug (Custom post type: trip)
        'Settings',                 // Page title
        'Settings',                 // Menu title
        'manage_options',           // Capability required to access the page
        'settings',                 // Submenu slug
        'settings_page_content'     // Callback function to display the content
    );
}
add_action('admin_menu', 'add_settings_page_submenu');

function settings_page_content() {
    ?>
    <div class="wrap">
        <h2>Settings</h2>
        <form method="post" action="">
            <br>
            <label for="google_api_key">Google API Key:</label><br><br>
            <input type="text" id="google_api_key" class="full-width" name="google_api_key" placeholder="enter api key" value="<?php echo esc_attr(get_option('google_api_key')); ?>" /><br><br>
            <input type="submit" class="button-primary" value="Save" />
        </form>
    </div>
    <?php
}



function save_google_api_key() {
    if (isset($_POST['google_api_key'])) {
        update_option('google_api_key', sanitize_text_field($_POST['google_api_key']));
    }
}
add_action('admin_init', 'save_google_api_key');
