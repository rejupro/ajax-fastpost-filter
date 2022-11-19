<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://facebook.com/rejutpi
 * @since             1.0.0
 * @package           Ajax_Fastpost_Filter
 *
 * @wordpress-plugin
 * Plugin Name:       Ajax FastPost Filter
 * Plugin URI:        https://https://facebook.com/rejutpi
 * Description:       This plugin for post filter, here we will use ajax for post searching and filitering by category, author and date.
 * Version:           1.0.0
 * Author:            Reedwanul Haque
 * Author URI:        https://https://facebook.com/rejutpi
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ajax-fastpost-filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AJAX_FASTPOST_FILTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ajax-fastpost-filter-activator.php
 */
function activate_ajax_fastpost_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ajax-fastpost-filter-activator.php';
	Ajax_Fastpost_Filter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ajax-fastpost-filter-deactivator.php
 */
function deactivate_ajax_fastpost_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ajax-fastpost-filter-deactivator.php';
	Ajax_Fastpost_Filter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ajax_fastpost_filter' );
register_deactivation_hook( __FILE__, 'deactivate_ajax_fastpost_filter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ajax-fastpost-filter.php';


include_once(dirname( __FILE__ ). '/includes/Fast_Testimonial_Loader.php');
if ( function_exists( 'fast_testimonial_run' ) ) {
    fast_testimonial_run();
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ajax_fastpost_filter() {

	$plugin = new Ajax_Fastpost_Filter();
	$plugin->run();

}
run_ajax_fastpost_filter();
