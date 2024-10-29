<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Admin_Bug_Report
 * @subpackage Admin_Bug_Report/admin
 * @author     David B <david@dream-encode.com>
 */
class Admin_Bug_Report_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Options
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $options    Array of plugin options stored in the database
	 */
	protected $options = array();

	/**
	 * Settings Fields
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $fields    Settings Fields
	 */
	protected $fields = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    	$plugin_name       	The name of this plugin.
	 * @param      string    	$version    		The version of this plugin.
	 * @param      array    	$options    		Plugins options.
	 */
	public function __construct( $plugin_name, $version, $options = array() ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( "{$this->plugin_name}", plugins_url( '/', __FILE__ ) . 'assets/dist/bugReportCss.min.css', array( 'wp-components' ), $this->version );

		$screen = get_current_screen();

		if ( "settings_page_{$this->plugin_name}-settings" === $screen->id ) {
			wp_enqueue_style( "{$this->plugin_name}-settings", plugins_url( '/', __FILE__ ) . 'assets/dist/settingsCss.min.css', array( 'wp-components' ), $this->version );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		// Localization/JS Vars
		$globals = array();

        if ( isset( $this->options['email_include_debug'] ) && $this->options['email_include_debug'] ) {
			global $post;

			if ( is_admin() ) {
				$globals['SCREEN'] = $screen;
			}

			if ( is_a( $post, 'WP_Post' ) ) {
				$globals['POST'] = $post;
			}
        }

		$localization = array(
			'AJAX_URL' => admin_url( 'admin-ajax.php' ),
			'NONCES'   => array(
				'SUBMIT_REPORT' => wp_create_nonce( 'SUBMIT_REPORT' ),
			),
			'SETTINGS' => $this->options,
			'GLOBALS'  => $globals,
		);

		wp_register_script( $this->plugin_name, plugins_url( '/', __FILE__ ) . 'assets/dist/bugReportJs.min.js', array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), time(), true );

		wp_localize_script( $this->plugin_name, 'DE_ABR', $localization );

		wp_enqueue_script( $this->plugin_name );

		if ( "settings_page_{$this->plugin_name}-settings" === $screen->id ) {
			wp_register_script( "{$this->plugin_name}-settings", plugin_dir_url( __FILE__ ) . 'assets/dist/settingsJs.min.js', array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element', $this->plugin_name ), time(), true );

			wp_enqueue_script( "{$this->plugin_name}-settings" );
		}
	}

    /**
     * Add plugin actions links
     *
     * @param array  $links
     *
     * @return array
     *
     * @since    1.0.0
	 * @access   public
     */
    public function plugin_action_links( $links ) {
        $links[] = '<a href="'. esc_url( menu_page_url( 'admin_bug_report', false ) ) .'">' . __( 'Settings', $this->plugin_name ) . '</a>';

        return $links;
    }


	/**
	 * Add the options page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_page() {
		$parent_slug = 'options-general.php';
		$page_title = __( 'Admin Bug Report Settings', $this->plugin_name );
		$menu_title = __( 'Admin Bug Report', $this->plugin_name );
		$capability = 'manage_options';
		$slug = 'admin_bug_report';
		$callback = array( $this, 'admin_page_display' );
		$position = 999;

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $slug, $callback, $position );
    }

	/**
	 * Add custom page links to the admin menu
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page(
			'options-general.php',
			__( 'Admin Bug Report Settings', $this->plugin_name ),
			__( 'Admin Bug Report', $this->plugin_name ),
			'manage_options',
			"{$this->plugin_name}-settings",
			array( $this, 'admin_menu_settings_callback' )
		);
	}

	/**
	 * Admin menu item callback (Settings)
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function admin_menu_settings_callback() {
		echo '<div id="de-admin-bug-report-settings"></div>';
	}

	/**
	 * Register our custom settings using the Settings API.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function register_settings() {
		register_setting(
			'de_admin_bug_report',
			'de_admin_bug_report',
			array(
				'type'         => 'object',
				'show_in_rest' =>  array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'email_recipient' => array(
								'type' => 'string',
							),
							'email_subject' => array(
								'type' => 'string',
							),
							'email_include_debug' => array(
								'type' => 'boolean',
							),
						),
					),
				),
			)
		);
	}

	/**
	 * HTMl for the bug report icon and form
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function output_bug_report() {
		echo '<div id="de-admin-bug-report"></div>';
	}

	/**
	 * Output debug globals in a pretty format
     *
     * @param array  $globals		Array of values/arrays to be output
     * @param mixed  $output_array	Referenced array of text "lines" to be output in the email
     * @return bool
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function email_output_debug_globals( $globals, array &$output_array ) {
		if ( $globals = json_decode( stripslashes( $_POST['globals'] ), true ) ) {
            $this->email_output_debug_globals_walk_array( $globals, $output_array );
        }
    }

	/**
	 * Recusrively walk through the globals array
     *
     * @param array  $globals		Array of values/arrays to be output
     * @param mixed  $output_array	Referenced array of text "lines" to be output in the email
     * @param mixed  $level			Pointer for the current nested level in the array
     * @return bool
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function email_output_debug_globals_walk_array( array $globals, array &$output_array, int $level = 0 ) {
		foreach ( $globals as $key => $value ) {
			$level_indent = esc_html( str_repeat( '&nbsp;', ( $level * 2 ) ) );

			if ( is_array( $value ) || is_object( $value ) ) {
				$output_array[] = sprintf( '%1$s%2$s', $level_indent, $key );

				$this->email_output_debug_globals_walk_array( $value, $output_array, ++$level );

				$output_array[] = '';
				$level -= 1;
			} else {
				$output_array[] = sprintf( '%1$s<strong>%2$s:</strong>  %3$s', $level_indent, $key, is_bool( $value ) ? intval( $value ) : $value );
			}
		}
    }

	/**
	 * A small helper to filter the default content type for wp_mail to HTML
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_html_mail_content_type() {
		return 'text/html';
	}

	/**
	 * Process a screenshot from a base64_encoded string.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @param    string $screenshot base64_encoded data url.
	 * @return   string|boolean
	 */
	protected function process_screenshot_to_uploads( $screenshot ) {
		$uploads_dir = wp_upload_dir();

		if ( ! empty( $uploads_dir['basedir'] ) ) {
			if ( preg_match( "/^data:image\/(\w+);base64,/", $screenshot, $type ) ) {
				$screenshot_data = substr( $screenshot, strpos( $screenshot, ',' ) + 1 );
				$type = strtolower( $type[1] );

				if ( ! in_array( $type, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) {
					return false;
				}
				$screenshot_data = str_replace( ' ', '+', $screenshot_data );
				$screenshot_data = base64_decode( $screenshot_data );

				if ( false !== $screenshot_data ) {
					$filename = sprintf(
						'%1$s%2$d.%3$s',
						trailingslashit( $uploads_dir['basedir'] ),
						time(),
						$type
					);

					if ( false !== file_put_contents( $filename, $screenshot_data ) ) {
						return $filename;
					}
				}
			}
		}

		return false;
	}

	/**
	 * AJAX handle for report submission
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function handle_report_submit() {
        if ( ! isset( $_POST['security'] ) ) {
			wp_send_json_error( 'missing_fields' );
			exit;
		}

		if ( ! check_ajax_referer( 'SUBMIT_REPORT', 'security' ) ) {
			wp_send_json_error( 'bad_nonce' );
			exit;
		}

		$email   = $this->options['email_recipient'];
		$subject = $this->options['email_subject'];

		$message    = filter_input( INPUT_POST, 'message', FILTER_SANITIZE_STRING );
		$screenshot = filter_input( INPUT_POST, 'screenshot', FILTER_SANITIZE_STRING );

		$attachment = '';

        if ( ! empty( $screenshot ) ) {
			$attachment = $this->process_screenshot_to_uploads( $screenshot );
		}

		//  Add some information to the message
		$info_array = array(
			apply_filters( 'de/admin_bug_report/email_message_prepend', __( 'A new bug report has been submitted!', $this->plugin_name ) ),
			'',
			sprintf( __( 'Site:  %s', $this->plugin_name ), get_home_url() ),
		);

		$current_user = wp_get_current_user();

		if ( $current_user instanceof WP_User ) {
			$info_array[] = sprintf( __( 'Username:  %s', $this->plugin_name ), esc_html( $current_user->user_login ) );
			$info_array[] = sprintf( __( 'Email:  %s', $this->plugin_name ), esc_html( $current_user->user_email ) );
		}

		$info_array[] = '';
		$info_array[] = __( 'User Message:', $this->plugin_name );
		$info_array[] = sanitize_textarea_field( $_POST['message'] );

		if ( isset( $this->options['email_include_debug'] ) && $this->options['email_include_debug'] ) {
			$separator = str_repeat( '=', 30 );
			$info_array[] = '';
			$info_array[] = sprintf( '%1$s %2$s %3$s', $separator, __( 'Debugging Information', $this->plugin_name ), $separator );

			if ( isset( $_POST['globals'] ) ) {
				$this->email_output_debug_globals( $_POST['globals'], $info_array );
			}
		}

		$message = implode( "<br />", $info_array );

		// We need to temporarily filter the default mail content type to output a nice email.
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_mail_content_type' ) );

		$message_allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
		);

		$result = wp_mail(
			sanitize_email( $email ),
			sanitize_text_field( $subject ),
			wp_kses( $message, $message_allowed_html ),
			array(),
			array( $attachment )
		);

		// Be a good neighbor and reset the default mail content type.
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_mail_content_type' ) );

        if ( ! $result ) {
			wp_send_json_error();
        } else {
			wp_send_json_success();
		}
    }
}
