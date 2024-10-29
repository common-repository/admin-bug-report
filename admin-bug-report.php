<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://dream-encode.com
 * @since             1.0.0
 * @package           Admin_Bug_Report
 *
 * @wordpress-plugin
 * Plugin Name:       Admin Bug Report
 * Plugin URI:        https://wordpress.org/plugins/admin-bug-report/
 * Description:       A small plugin to help your WordPress clients report bugs and issues directly from the admin.
 * Version:           2.1.0
 * Author:            David B
 * Author URI:        https://dream-encode.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-bug-report
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ADMIN_BUG_REPORT_VERSION', '2.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-admin-bug-report-activator.php
 */
function activate_admin_bug_report() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-bug-report-activator.php';
	Admin_Bug_Report_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-admin-bug-report-deactivator.php
 */
function deactivate_admin_bug_report() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-bug-report-deactivator.php';
	Admin_Bug_Report_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_admin_bug_report' );
register_deactivation_hook( __FILE__, 'deactivate_admin_bug_report' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-admin-bug-report.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_admin_bug_report() {
	$plugin = new Admin_Bug_Report();
	$plugin->run();
}

run_admin_bug_report();
