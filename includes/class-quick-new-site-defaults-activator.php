<?php

/**
 * Fired during plugin activation
 *
 * @link       http://stehle-internet.de/
 * @since      1.0.0
 *
 * @package    Quick_New_Site_Defaults
 * @subpackage Quick_New_Site_Defaults/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Quick_New_Site_Defaults
 * @subpackage Quick_New_Site_Defaults/includes
 * @author     Martin Stehle <shop@stehle-internet.de>
 */
class Quick_New_Site_Defaults_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// store the flag into the db to trigger the display of a message after activation
		set_transient( 'quick-new-site-defaults', '1', 60 );

	}

}
