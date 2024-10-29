<?php

/**
 * Fired during plugin activation
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/includes
 * @author     David B <david@dream-encode.com>
 */
class Admin_Bug_Report_Activator {
	/**
	 * Activator
	 *
	 * Run this code during plugin acivation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option(
			'de_admin_bug_report',
			array(
				'email_recipient'         => 'user@example.com',
				'email_subject'           => 'New Admin Bug Report',
				'email_include_debugging' => true,
			),
			true
		);
	}
}
