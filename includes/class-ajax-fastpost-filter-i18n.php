<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://facebook.com/rejutpi
 * @since      1.0.0
 *
 * @package    Ajax_Fastpost_Filter
 * @subpackage Ajax_Fastpost_Filter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ajax_Fastpost_Filter
 * @subpackage Ajax_Fastpost_Filter/includes
 * @author     Reedwanul Haque <reedwanultpi@gmail.com>
 */
class Ajax_Fastpost_Filter_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ajax-fastpost-filter',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
