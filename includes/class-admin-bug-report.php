<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/includes
 * @author     David B <david@dream-encode.com>
 */

class Admin_Bug_Report {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Admin_Bug_Report_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Options container
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $options    Array of plugin options from the DB
	 */
	public $options = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ADMIN_BUG_REPORT_VERSION' ) ) {
			$this->version = ADMIN_BUG_REPORT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'admin-bug-report';

		$this->load_options();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Admin_Bug_Report_Loader. Orchestrates the hooks of the plugin.
	 * - Admin_Bug_Report_i18n. Defines internationalization functionality.
	 * - Admin_Bug_Report_Admin. Defines all hooks for the admin area.
	 * - Admin_Bug_Report_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-admin-bug-report-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-admin-bug-report-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin-bug-report-admin.php';

		$this->loader = new Admin_Bug_Report_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Admin_Bug_Report_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Admin_Bug_Report_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Admin_Bug_Report_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_options() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'register_settings' );

        $this->loader->add_filter( 'plugin_action_links_admin-bug-report/plugins-stats-dashboard.php', $plugin_admin, 'plugin_action_links' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );

		$this->loader->add_action( 'admin_footer', $plugin_admin, 'output_bug_report' );
		$this->loader->add_action( 'wp_ajax_admin_bug_report_submit', $plugin_admin, 'handle_report_submit' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Admin_Bug_Report_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Set plugin options
	 *
	 * @access private
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return bool
	 */
	private function load_options() {
		$this->options = get_option( 'de_admin_bug_report', array() );
	}

	/**
	 * Get plugin options
	 *
	 * @access private
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return bool
	 */
	private function get_options() {
		return $this->options;
	}

}
