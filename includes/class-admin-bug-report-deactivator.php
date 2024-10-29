<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/includes
 * @author     David B <david@dream-encode.com>
 */
class Admin_Bug_Report_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'de_admin_bug_report' );
	}
}
