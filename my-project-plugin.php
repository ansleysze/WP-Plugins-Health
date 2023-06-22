<?php
/**
 * Plugin Name: Plugin Lister
 * Description: Displays a list of installed plugins on the wp-admin/ page like how typing WP List plugin in your site command prompt
 It will output these in JSON format!
 * Version: 1.0
 */

// Register a custom admin menu page
function list_plugins_menu() {
  add_menu_page('List Plugins', 'List Plugins', 'manage_options', 'list-plugins', 'list_plugins_page');
}
add_action('admin_menu', 'list_plugins_menu');

// Callback function for the admin menu page
function list_plugins_page() {
  $plugins = get_plugins();
  $plugin_list = array();
  
  foreach ($plugins as $plugin_file => $plugin_data) {
    $status = is_plugin_active($plugin_file) ? 'Active' : 'Inactive';
    $update_status = get_site_transient('update_plugins');
    $update = isset($update_status->response[$plugin_file]) ? 'Update Available' : 'Up to Date';

    // Extract the slug name without the file extension
    $slug = basename($plugin_file, '.php');
  
    $plugin_list[] = array(
      'slug' => $slug,
      'status' => $status,
      'update_status' => $update,
      'version' => $plugin_data['Version']
    );
  }
  
  $output = json_encode($plugin_list, JSON_PRETTY_PRINT);
  
  echo '<div class="wrap">';
  echo '<h1>List of Installed Plugins</h1>';
  echo '<pre>' . $output . '</pre>';
  echo '</div>';
}
