<?php
/**
 * Plugin Name: Plugins&Health
 * Description: Displays a list of installed plugins and site health information on the wp-admin/ page.
 * Version: 1.1
 */

// Register a custom admin menu page
function list_plugins_site_health_menu() {
  add_menu_page('List Plugins & Site Health', 'List Plugins & Site Health', 'manage_options', 'list-plugins-site-health', 'list_plugins_site_health_page');
}
add_action('admin_menu', 'list_plugins_site_health_menu');

// Callback function for the admin menu page
function list_plugins_site_health_page() {
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

  // Get site health information
  $site_health = array(
  'php_version' => PHP_VERSION,
  'mysql_version' => $GLOBALS['wpdb']->db_version(),
  'theme' => wp_get_theme()->get('Name'),
  'active_plugins_count' => count(get_option('active_plugins')),
  'wordpress_version' => get_bloginfo('version'),
  'home_url' => get_home_url(),
  'admin_email' => get_option('admin_email'),
  // Add more site health information here
);

  $output = array(
    'plugins' => $plugin_list,
    'site_health' => $site_health
  );

  $output_json = json_encode($output, JSON_PRETTY_PRINT);

  echo '<div class="wrap">';
  echo '<h1>List of Installed Plugins & Site Health</h1>';
  echo '<pre>' . $output_json . '</pre>';
  echo '</div>';
}
