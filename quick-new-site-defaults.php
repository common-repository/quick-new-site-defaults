<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://stehle-internet.de/
 * @since             1.0.0
 * @package           Quick_New_Site_Defaults
 *
 * @wordpress-plugin
 * Plugin Name:       Quick New Site Defaults
 * Plugin URI:        https://wordpress.org/plugins/quick-new-site-defaults/
 * Description:       Set default settings of a freshly installed site quickly.
 * Version:           1.2.2
 * Author:            Martin Stehle
 * Author URI:        http://stehle-internet.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quick-new-site-defaults
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-quick-new-site-defaults-activator.php
 */
function activate_quick_new_site_defaults() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quick-new-site-defaults-activator.php';
	Quick_New_Site_Defaults_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quick-new-site-defaults-deactivator.php
 */
function deactivate_quick_new_site_defaults() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quick-new-site-defaults-deactivator.php';
	Quick_New_Site_Defaults_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_quick_new_site_defaults' );
register_deactivation_hook( __FILE__, 'deactivate_quick_new_site_defaults' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-quick-new-site-defaults.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_quick_new_site_defaults() {

	$plugin = new Quick_New_Site_Defaults();
	$plugin->run();

}
run_quick_new_site_defaults();
