<?php
/**
 * The WP_Members Class.
 *
 * This is the main WP_Members object class. This class contains functions
 * for loading settings, shortcodes, hooks to WP, plugin dropins, constants,
 * and registration fields. It also manages whether content should be blocked.
 *
 * @package WP-Members
 * @subpackage WP_Members Object Class
 * @since 3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WP_Members {
	
	/**
	 * Plugin version.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $version = WPMEM_VERSION;
	
	/**
	 * Database version
	 *
	 * @since  3.2.2
	 * @access public
	 * @var    string
	 */
	public $db_version = WPMEM_DB_VERSION;
	
	/**
	 * Content block settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $block;
	
	/**
	 * Excerpt settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $show_excerpt;
	
	/**
	 * Show login form settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $show_login;
	
	/**
	 * Show registration form settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $show_reg;
	
	/**
	 * Auto-excerpt settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $autoex;
	
	/**
	 * Notify admin settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $notify;
	
	/**
	 * Moderated registration settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $mod_reg;
	
	/**
	 * Captcha settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $captcha;
	
	/**
	 * Enable expiration extension settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $use_exp;
	
	/**
	 * Expiration extension enable trial period.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $use_trial;
	
	/**
	 * 
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $warnings;
	
	/**
	 * Enable drop-ins setting.
	 *
	 * @since  3.1.9
	 * @access public
	 * @var    string
	 */
	public $dropins = 0;
	
	/**
	 * Container for enabled dropins.
	 *
	 * @since  3.1.9
	 * @access public
	 * @var    array
	 */
	public $dropins_enabled = array();

	/**
	 * Current plugin action container.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $action;
	
	/**
	 * Regchk container.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    string
	 */
	public $regchk;
	
	/**
	 * User page settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $user_pages;
	
	/**
	 * Custom Post Type settings.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    array
	 */
	public $post_types;
	
	/**
	 * Setting for applying texturization.
	 *
	 * @since  3.1.7
	 * @access public
	 * @var    boolean
	 */
	public $texturize;
	
	/**
	 * Enable product creation.
	 *
	 * @since 3.2.0
	 * @access public
	 * @var boolean
	 */
	public $enable_products;
	
	/**
	 * Enable logged-in menu clones.
	 *
	 * @since  3.2.0
	 * @access public
	 * @var    string
	 */
	public $clone_menus;
	
	/**
	 * Container for error messages.
	 *
	 * @since  3.2.0
	 * @access public
	 * @var    string
	 */
	public $error;
	
	/**
	 * Plugin initialization function.
	 *
	 * @since 3.0.0
	 * @since 3.1.6 Dependencies now loaded by object.
	 */
	function __construct() {
		
		// Load dependent files.
		$this->load_dependencies();
	
		/**
		 * Filter the options before they are loaded into constants.
		 *
		 * @since 2.9.0
		 * @since 3.0.0 Moved to the WP_Members class.
		 *
		 * @param array $this->settings An array of the WP-Members settings.
		 */
		$settings = apply_filters( 'wpmem_settings', get_option( 'wpmembers_settings' ) );

		// Validate that v3 settings are loaded.
		if ( ! isset( $settings['version'] ) 
			|| $settings['version'] != $this->version
			|| ! isset( $settings['db_version'] ) 
			|| $settings['db_version'] != $this->db_version ) {
			/**
			 * Load installation routine.
			 */
			require_once( WPMEM_PATH . 'inc/install.php' );
			// Update settings.
			/** This filter is documented in /inc/class-wp-members.php */
			$settings = apply_filters( 'wpmem_settings', wpmem_do_install() );
		}
		
		// Assemble settings.
		foreach ( $settings as $key => $val ) {
			$this->$key = $val;
		}
		
		$this->load_user_pages();
		$this->cssurl     = ( isset( $this->style ) && $this->style == 'use_custom' ) ? $this->cssurl : $this->style; // Set the stylesheet.
		$this->forms      = new WP_Members_Forms;         // Load forms.
		$this->api        = new WP_Members_API;           // Load api.
		$this->user       = new WP_Members_User( $this ); // Load user functions.
		$this->shortcodes = new WP_Members_Shortcodes();  // Load shortcodes.
		$this->membership = new WP_Members_Products();    // Load membership plans
		$this->email      = new WP_Members_Email;         // Load email functions
		$this->menus      = ( $this->clone_menus ) ? new WP_Members_Menus() : null; // Load clone menus.
		
		/**
		 * Fires after main settings are loaded.
		 *
		 * @since 3.0
		 * @deprecated 3.2.0 Use wpmem_after_init instead.
		 */
		do_action( 'wpmem_settings_loaded' );
	
		// Preload the expiration module, if available.
		$exp_active = ( function_exists( 'wpmem_exp_init' ) || function_exists( 'wpmem_set_exp' ) ) ? true : false;
		define( 'WPMEM_EXP_MODULE', $exp_active ); 
	
		// Load actions and filters.
		$this->load_hooks();
		
		// Load contants.
		$this->load_constants();
		
		// Load dropins.
		if ( $this->dropins ) {
			$this->load_dropins();
		}
	}
	
	/**
	 * Plugin initialization function to load hooks.
	 *
	 * @since 3.0.0
	 */
	function load_hooks() {
		
		/**
		 * Fires before action and filter hooks load.
		 *
		 * @since 3.0.0
		 * @since 3.1.6 Fires before hooks load.
		 */
		do_action( 'wpmem_load_hooks' );

		// Add actions.
		add_action( 'template_redirect',     array( $this, 'get_action'  ) );
		add_action( 'widgets_init',          array( $this, 'widget_init' ) );  // initializes the widget
		add_action( 'admin_init',            array( $this, 'load_admin'  ) ); // check user role to load correct dashboard
		add_action( 'admin_menu',            'wpmem_admin_options' );      // adds admin menu
		add_action( 'user_register',         'wpmem_wp_reg_finalize' );    // handles wp native registration
		add_action( 'login_enqueue_scripts', 'wpmem_wplogin_stylesheet' ); // styles the native registration
		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_style' ) );  // Enqueues the stylesheet.
		add_action( 'wp_enqueue_scripts',    array( $this, 'loginout_script' ) );
		add_action( 'init',                  array( $this, 'load_textdomain' ) ); //add_action( 'plugins_loaded', 'wpmem_load_textdomain' );
		add_action( 'init',                  array( $this->membership, 'add_cpt' ), 0 ); // Adds membership plans custom post type.
		add_action( 'pre_get_posts',         array( $this, 'do_hide_posts' ) );
		add_action( 'customize_register',    array( $this, 'customizer_settings' ) );

		if ( is_user_logged_in() ) {
			add_action( 'wpmem_pwd_change',  array( $this->user, 'set_password' ), 9, 2 );
			add_action( 'wpmem_pwd_change',  array( $this->user, 'set_as_logged_in' ), 10 );
		}
		
		// Add filters.
		add_filter( 'the_content',               array( $this, 'do_securify' ), 99 );
		add_filter( 'allow_password_reset',      array( $this->user, 'no_reset' ) );           // no password reset for non-activated users
		add_filter( 'register_form',             'wpmem_wp_register_form' );                   // adds fields to the default wp registration
		add_action( 'woocommerce_register_form', 'wpmem_woo_register_form' );
		add_filter( 'registration_errors',       'wpmem_wp_reg_validate', 10, 3 );             // native registration validation
		add_filter( 'comments_open',             array( $this, 'do_securify_comments' ), 99 ); // securifies the comments
		add_filter( 'wpmem_securify',            array( $this, 'reg_securify' ) );             // adds success message on login form if redirected
		//add_filter( 'query_vars',                array( $this, 'add_query_vars' ), 10, 2 );           // adds custom query vars
		add_filter( 'get_pages',                 array( $this, 'filter_get_pages' ) );
		add_filter( 'wp_get_nav_menu_items',     array( $this, 'filter_nav_menu_items' ), null, 3 );
		add_filter( 'get_previous_post_where',   array( $this, 'filter_get_adjacent_post_where' ) );
		add_filter( 'get_next_post_where',       array( $this, 'filter_get_adjacent_post_where' ) );
		
		// If registration is moderated, check for activation (blocks backend login by non-activated users).
		if ( $this->mod_reg == 1 ) { 
			add_filter( 'authenticate', array( $this->user, 'check_activated' ), 99, 3 ); 
		}

		/**
		 * Fires after action and filter hooks load.
		 *
		 * @since 3.0.0
		 * @since 3.1.6 Was wpmem_load_hooks, now wpmem_hooks_loaded.
		 */
		do_action( 'wpmem_hooks_loaded' );
	}
	
	/**
	 * Load drop-ins.
	 *
	 * @since 3.0.0
	 *
	 * @todo This is experimental. The function and its operation is subject to change.
	 */
	function load_dropins() {

		/**
		 * Fires before dropins load (for adding additional drop-ins).
		 *
		 * @since 3.0.0
		 * @since 3.1.6 Fires before dropins.
		 */
		do_action( 'wpmem_load_dropins' );
		
		/**
		 * Filters the drop-in file folder.
		 *
		 * @since 3.0.0
		 *
		 * @param string $folder The drop-in file folder.
		 */
		$folder = apply_filters( 'wpmem_dropin_folder', WPMEM_DROPIN_DIR );
		
		// Load any drop-ins.
		$settings = get_option( 'wpmembers_dropins' );
		$this->dropins_enabled = ( $settings ) ? $settings : array();
		if ( ! empty( $this->dropins_enabled ) ) {
			foreach ( $this->dropins_enabled as $filename ) {
				$dropin = $folder . $filename;
				if ( file_exists( $dropin ) ) {
					include_once( $dropin );
				}
			}
		}

		/**
		 * Fires before dropins load (for adding additional drop-ins).
		 *
		 * @since 3.0.0
		 * @since 3.1.6 Was wpmem_load_dropins, now wpmem_dropins_loaded.
		 */
		do_action( 'wpmem_dropins_loaded' );
	}
	
	/**
	 * Loads pre-3.0 constants (included primarily for add-on compatibility).
	 *
	 * @since 3.0.0
	 */
	function load_constants() {
		( ! defined( 'WPMEM_BLOCK_POSTS'  ) ) ? define( 'WPMEM_BLOCK_POSTS',  $this->block['post']  ) : '';             // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_BLOCK_PAGES'  ) ) ? define( 'WPMEM_BLOCK_PAGES',  $this->block['page']  ) : '';             // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_SHOW_EXCERPT' ) ) ? define( 'WPMEM_SHOW_EXCERPT', $this->show_excerpt['post'] ) : '';       // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_NOTIFY_ADMIN' ) ) ? define( 'WPMEM_NOTIFY_ADMIN', $this->notify    ) : '';                  // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_MOD_REG'      ) ) ? define( 'WPMEM_MOD_REG',      $this->mod_reg   ) : '';                  // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_CAPTCHA'      ) ) ? define( 'WPMEM_CAPTCHA',      $this->captcha   ) : '';                  // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_NO_REG'       ) ) ? define( 'WPMEM_NO_REG',       ( -1 * $this->show_reg['post'] ) ) : '';  // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_USE_EXP'      ) ) ? define( 'WPMEM_USE_EXP',      $this->use_exp   ) : '';
		( ! defined( 'WPMEM_USE_TRL'      ) ) ? define( 'WPMEM_USE_TRL',      $this->use_trial ) : '';
		( ! defined( 'WPMEM_IGNORE_WARN'  ) ) ? define( 'WPMEM_IGNORE_WARN',  $this->warnings  ) : '';                  // @todo Can deprecate? Probably 3.3

		( ! defined( 'WPMEM_MSURL'  ) ) ? define( 'WPMEM_MSURL',  $this->user_pages['profile']  ) : '';                 // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_REGURL' ) ) ? define( 'WPMEM_REGURL', $this->user_pages['register'] ) : '';                 // @todo Can deprecate? Probably 3.3
		( ! defined( 'WPMEM_LOGURL' ) ) ? define( 'WPMEM_LOGURL', $this->user_pages['login']    ) : '';                 // @todo Can deprecate? Probably 3.3

		( ! defined( 'WPMEM_DROPIN_DIR' ) ) ? define( 'WPMEM_DROPIN_DIR', WP_PLUGIN_DIR . '/wp-members-dropins/' ) : '';
		
		define( 'WPMEM_CSSURL', $this->cssurl );
	}

	/**
	 * Load dependent files.
	 *
	 * @since 3.1.6
	 */
	function load_dependencies() {
		
		/**
		 * Filter the location and name of the pluggable file.
		 *
		 * @since 2.9.0
		 * @since 3.1.6 Moved in load order to come before dependencies.
		 *
		 * @param string The path to WP-Members plugin functions file.
		 */
		$wpmem_pluggable = apply_filters( 'wpmem_plugins_file', WP_PLUGIN_DIR . '/wp-members-pluggable.php' );
	
		// Preload any custom functions, if available.
		if ( file_exists( $wpmem_pluggable ) ) {
			include( $wpmem_pluggable );
		}
		
		require_once( WPMEM_PATH . 'inc/class-wp-members-api.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-email.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-forms.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-menus.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-products.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-shortcodes.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-user.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-user-profile.php' );
		require_once( WPMEM_PATH . 'inc/class-wp-members-widget.php' );
		require_once( WPMEM_PATH . 'inc/api.php' );
		require_once( WPMEM_PATH . 'inc/api-email.php' );
		require_once( WPMEM_PATH . 'inc/api-forms.php' );
		require_once( WPMEM_PATH . 'inc/api-users.php' );
		require_once( WPMEM_PATH . 'inc/api-utilities.php' );
		require_once( WPMEM_PATH . 'inc/forms.php' );
		require_once( WPMEM_PATH . 'inc/dialogs.php' );
		require_once( WPMEM_PATH . 'inc/wp-registration.php' );
		require_once( WPMEM_PATH . 'inc/deprecated.php' );
		//require_once( WPMEM_PATH . 'inc/core.php' ); // @deprectated 3.2.4
		//require_once( WPMEM_PATH . 'inc/utilities.php' ); // @deprecated 3.2.3
		//require_once( WPMEM_PATH . 'inc/sidebar.php' ); // @deprecated 3.2.0
		//require_once( WPMEM_PATH . 'inc/shortcodes.php' ); // @deprecated 3.2.0
		//require_once( WPMEM_PATH . 'inc/email.php' ); // @deprecated 3.2.0
		//require_once( WPMEM_PATH . 'inc/users.php' ); // @deprecated 3.1.9

	}

	/**
	 * Load admin API and dependencies.
	 *
	 * Determines which scripts to load and actions to use based on the 
	 * current users capabilities.
	 *
	 * @since 2.5.2
	 * @since 3.1.0 Added admin api object.
	 * @since 3.1.7 Moved from main plugin file as wpmem_chk_admin() to main object.
	 */
	function load_admin() {

		/**
		 * Fires before initialization of admin options.
		 *
		 * @since 2.9.0
		 */
		do_action( 'wpmem_pre_admin_init' );

		// Initilize the admin api.
		$this->load_admin_api();

		/**
		 * Fires after initialization of admin options.
		 *
		 * @since 2.9.0
		 */
		do_action( 'wpmem_after_admin_init' );
	}
	
	/**
	 * Gets the requested action.
	 *
	 * @since 3.0.0
	 *
	 * @global string $wpmem_a The WP-Members action variable.
	 */
	function get_action() {

		// Get the action being done (if any).
		$this->action = sanitize_text_field( wpmem_get( 'a', '', 'request' ) );

		// For backward compatibility with processes that check $wpmem_a.
		global $wpmem_a;
		$wpmem_a = $this->action;
		
		/**
		 * Fires when the wpmem action is retrieved.
		 *
		 * @since 3.1.7
		 */
		do_action( 'wpmem_get_action' );

		// Get the regchk value (if any).
		$this->regchk = $this->get_regchk( $this->action );
	}
	
	/**
	 * Gets the regchk value.
	 *
	 * regchk is a legacy variable that contains information about the current
	 * action being performed. Login, logout, password, registration, profile
	 * update functions all return a specific value that is stored in regchk.
	 * This value and information about the current action can then be used to
	 * determine what content is to be displayed by the securify function.
	 *
	 * @since 3.0.0
	 *
	 * @global string $wpmem_a The WP-Members action variable.
	 *
	 * @param  string $action The current action.
	 * @return string         The regchk value.
	 */
	function get_regchk( $action ) {

		switch ( $action ) {

			case 'login':
				$regchk = $this->user->login();
				break;

			case 'logout':
				$regchk = $this->user->logout();
				break;
			
			case 'pwdchange':
				$regchk = $this->user->password_update( 'change' );
				break;

			case 'pwdreset':
				$regchk = $this->user->password_update( 'reset' );
				break;
			
			case 'getusername':
				$regchk = $this->user->retrieve_username();
				break;
			
			case 'register':
			case 'update':
				require_once( WPMEM_PATH . 'inc/register.php' );
				$regchk = wpmem_registration( $action  );
				break;

			default:
				$regchk = ( isset( $regchk ) ) ? $regchk : '';
				break;
		}
		
		/**
		 * Filter wpmem_regchk.
		 *
		 * The value of regchk is determined by functions that may be run in the get_regchk function.
		 * This value determines what happens in the wpmem_securify() function.
		 *
		 * @since 2.9.0
		 * @since 3.0.0 Moved to get_regchk() in WP_Members object.
		 *
		 * @param  string $this->regchk The value of wpmem_regchk.
		 * @param  string $this->action The $wpmem_a action.
		 */
		$regchk = apply_filters( 'wpmem_regchk', $regchk, $action );
		
		// Legacy global variable for use with older extensions.
		global $wpmem_regchk;
		$wpmem_regchk = $regchk;
		
		return $regchk;
	}
	
	/**
	 * Determines if content should be blocked.
	 *
	 * This function was originally stand alone in the core file and
	 * was moved to the WP_Members class in 3.0.
	 *
	 * @since 3.0.0
	 *
	 * @global object $post  The WordPress Post object.
	 * @return bool   $block true|false
	 */
	function is_blocked() {
	
		global $post;
		
		if ( $post ) {

			// Backward compatibility for old block/unblock meta.
			$meta = get_post_meta( $post->ID, '_wpmem_block', true );
			if ( ! $meta ) {
				// Check for old meta.
				$old_block   = get_post_meta( $post->ID, 'block',   true );
				$old_unblock = get_post_meta( $post->ID, 'unblock', true );
				$meta = ( $old_block ) ? 1 : ( ( $old_unblock ) ? 0 : $meta );
			}
	
			// Setup defaults.
			$defaults = array(
				'post_id'    => $post->ID,
				'post_type'  => $post->post_type,
				'block'      => ( isset( $this->block[ $post->post_type ] ) && $this->block[ $post->post_type ] == 1 ) ? true : false,
				'block_meta' => $meta, // @todo get_post_meta( $post->ID, '_wpmem_block', true ),
				'block_type' => ( isset( $this->block[ $post->post_type ] ) ) ? $this->block[ $post->post_type ] : 0,
			);
	
			/**
			 * Filter the block arguments.
			 *
			 * @since 2.9.8
			 * @since 3.0.0 Moved to is_blocked() in WP_Members object.
			 *
			 * @param array $args     Null.
			 * @param array $defaults Although you are not filtering the defaults, knowing what they are can assist developing more powerful functions.
			 */
			$args = apply_filters( 'wpmem_block_args', '', $defaults );
	
			// Merge $args with defaults.
			$args = ( wp_parse_args( $args, $defaults ) );
	
			if ( is_single() || is_page() ) {
				switch( $args['block_type'] ) {
					case 1: // If content is blocked by default.
						$args['block'] = ( $args['block_meta'] == '0' ) ? false : $args['block'];
						break;
					case 0 : // If content is unblocked by default.
						$args['block'] = ( $args['block_meta'] == '1' ) ? true : $args['block'];
						break;
				}

			} else {
				$args['block'] = false;
			}

		} else {
			$args = array( 'block' => false );
		}
	
		// Don't block user pages.
		$args['block'] = ( in_array( get_permalink(), $this->user_pages ) ) ? false : $args['block'];

		/**
		 * Filter the block boolean.
		 *
		 * @since 2.7.5
		 *
		 * @param bool  $args['block']
		 * @param array $args
		 */
		return apply_filters( 'wpmem_block', $args['block'], $args );
	}
	
	/**
	 * The Securify Content Filter.
	 *
	 * This is the primary function that picks up where get_action() leaves off.
	 * Determines whether content is shown or hidden for both post and pages. This
	 * is a filter function for the_content.
	 *
	 * @link https://developer.wordpress.org/reference/functions/the_content/
	 * @link https://developer.wordpress.org/reference/hooks/the_content/
	 *
	 * @since 3.0.0
	 *
	 * @global object $post         The WordPress Post object.
	 * @global object $wpmem        The WP_Members object.
	 * @global string $wpmem_themsg Contains messages to be output.
	 * @param  string $content
	 * @return string $content
	 */
	function do_securify( $content = null ) {

		global $post, $wpmem, $wpmem_themsg;

		$content = ( is_single() || is_page() ) ? $content : wpmem_do_excerpt( $content );

		if ( ( ! has_shortcode( $content, 'wp-members' ) ) ) {

			if ( $this->regchk == "captcha" ) {
				global $wpmem_captcha_err;
				$wpmem_themsg = $wpmem->get_text( 'reg_captcha_err' )  . '<br /><br />' . $wpmem_captcha_err;
			}

			// Block/unblock Posts.
			if ( ! is_user_logged_in() && $this->is_blocked() == true ) {
				
				//Show the login and registration forms.
				if ( $this->regchk ) {
					
					// Empty content in any of these scenarios.
					$content = '';

					switch ( $this->regchk ) {

					case "loginfailed":
						$content = wpmem_inc_loginfailed();
						break;

					case "success":
						$content = wpmem_inc_regmessage( $this->regchk, $wpmem_themsg );
						$content = $content . wpmem_inc_login();
						break;

					default:
						$content = wpmem_inc_regmessage( $this->regchk, $wpmem_themsg );
						$content = $content . wpmem_inc_registration();
						break;
					}

				} else {

					// Toggle shows excerpt above login/reg on posts/pages.
					global $wp_query;
					if ( isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 1 ) {

							// Shuts down excerpts on multipage posts if not on first page.
							$content = '';

					} elseif ( isset( $this->show_excerpt[ $post->post_type ] ) && $this->show_excerpt[ $post->post_type ] == 1 ) {

						if ( ! stristr( $content, '<span id="more' ) ) {
							$content = wpmem_do_excerpt( $content );
						} else {
							$len = strpos( $content, '<span id="more' );
							$content = substr( $content, 0, $len );
						}

					} else {

						// Empty all content.
						$content = '';

					}

					$content = ( isset( $this->show_login[ $post->post_type ] ) && $this->show_login[ $post->post_type ] == 1 ) ? $content . wpmem_inc_login() : $content . wpmem_inc_login( 'page', '', 'hide' );

					$content = ( isset( $this->show_reg[ $post->post_type ] ) && $this->show_reg[ $post->post_type ] == 1 ) ? $content . wpmem_inc_registration() : $content;
				}

			// Protects comments if expiration module is used and user is expired.
			} elseif ( is_user_logged_in() && $this->is_blocked() == true ){

				if ( $this->use_exp == 1 && function_exists( 'wpmem_do_expmessage' ) ) {
					/**
					 * Filters the user expired message used by the PayPal extension.
					 *
					 * @since 3.2.0
					 *
					 * @param string $message
					 * @param string $content
					 */
					$content = apply_filters( 'wpmem_do_expmessage', wpmem_do_expmessage( $content ), $content );
				}
			}
		}

		/**
		 * Filter the value of $content after wpmem_securify has run.
		 *
		 * @since 2.7.7
		 * @since 3.0.0 Moved to new method in WP_Members Class.
		 *
		 * @param string $content The content after securify has run.
		 */
		$content = apply_filters( 'wpmem_securify', $content );

		if ( 1 == $this->texturize && strstr( $content, '[wpmem_txt]' ) ) {
			// Fix the wptexturize.
			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );
			add_filter( 'the_content', array( $this, 'texturize' ), 999 );
		}

		return $content;
		
	}
	
	/**
	 * Securifies the comments.
	 *
	 * If the user is not logged in and the content is blocked
	 * (i.e. wpmem->is_blocked() returns true), function loads a
	 * dummy/empty comments template.
	 *
	 * @since 2.9.9
	 * @since 3.2.0 Moved wpmem_securify_comments() to main class, renamed.
	 *
	 * @return bool $open true if current post is open for comments, otherwise false.
	 */
	function do_securify_comments( $open ) {

		$open = ( ! is_user_logged_in() && wpmem_is_blocked() ) ? false : $open;

		/**
		 * Filters whether comments are open or not.
		 *
		 * @since 3.0.0
		 * @since 3.2.0 Moved to main class.
		 *
		 * @param bool $open true if current post is open for comments, otherwise false.
		 */
		$open = apply_filters( 'wpmem_securify_comments', $open );

		if ( ! $open ) {
			/** This filter is documented in wp-includes/comment-template.php */
			add_filter( 'comments_array', array( $this, 'do_securify_comments_array' ), 10, 2 );
		}

		return $open;
	}
	
	/**
	 * Empties the comments array if content is blocked.
	 *
	 * @since 3.0.1
	 * @since 3.2.0 Moved wpmem_securify_comments_array() to main class, renamed.
	 *
	 * @global object $wpmem The WP-Members object class.
	 *
	 * @return array $comments The comments array.
	 */
	function do_securify_comments_array( $comments , $post_id ) {
		$comments = ( ! is_user_logged_in() && wpmem_is_blocked() ) ? array() : $comments;
		return $comments;
	}

	/**
	 * Adds the successful registration message on the login page if reg_nonce validates.
	 *
	 * @since 3.1.7
	 * @since 3.2.0 Moved to wpmem object, renamed reg_securify()
	 *
	 * @param  string $content
	 * @return string $content
	 */
	function reg_securify( $content ) {
		global $wpmem, $wpmem_themsg;
		$nonce = wpmem_get( 'reg_nonce', false, 'get' );
		if ( $nonce && wp_verify_nonce( $nonce, 'register_redirect' ) ) {
			$content = wpmem_inc_regmessage( 'success', $wpmem_themsg );
			$content = $content . wpmem_inc_login();
		}
		return $content;
	}

	/**
	 * Gets an array of hidden post IDs.
	 *
	 * @since 3.2.0
	 *
	 * @global object $wpdb
	 * @return array  $hidden
	 */
	function hidden_posts() {
		global $wpdb;
		$hidden = get_transient( '_wpmem_hidden_posts' );
		if ( false === $hidden ) {
			$hidden = $this->update_hidden_posts();
		}
		return $hidden;
	}
	
	/**
	 * Updates the hidden post array transient.
	 *
	 * @since 3.2.0
	 *
	 * @global object $wpdb
	 * @return array  $hidden
	 */
	function update_hidden_posts() {
		global $wpdb;
		$hidden  = array();
		$results = $wpdb->get_results( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_wpmem_block' AND meta_value = 2" );
		foreach( $results as $result ) {
			$hidden[] = $result->post_id;
		}
		set_transient( '_wpmem_hidden_posts', $hidden, 60*5 );
		return $hidden;
	}
	
	/**
	 * Gets an array of hidden post IDs.
	 *
	 * @since 3.2.0
	 *
	 * @return array  $hidden
	 */
	function get_hidden_posts() {
		$hidden = array();
		if ( ! is_admin() && ( ! is_user_logged_in() ) ) {
			$hidden = $this->hidden_posts();
		}
		// @todo Possibly separate query here to check. If the user IS logged in, check what posts they DON'T have access to.
		if ( ! is_admin() && is_user_logged_in() && 1 == $this->enable_products ) { 
			// Get user product access.
			// @todo This maybe should be a transient stored in the user object.
			$hidden = $this->hidden_posts();
			$hidden = ( is_array( $hidden ) ) ? $hidden : array();
			foreach ( $this->membership->products as $key => $value ) {
				if ( isset( $this->user->access[ $key ] ) && ( true === $this->user->access[ $key ] || $this->user->is_current( $this->user->access[ $key ] ) ) ) {
					foreach ( $hidden as $post_id ) {
						if ( 1 == get_post_meta( $post_id, $this->membership->post_stem . $key, true ) ) {
							$hidden_key = array_search( $post_id, $hidden );
							unset( $hidden[ $hidden_key ] );	
						}
					}
				}
			}
		}
		return $hidden;
	}
	
	/**
	 * Hides posts based on settings and meta.
	 *
	 * @since 3.2.0
	 *
	 * @param  array  $query
	 * @return array  $query
	 */
	function do_hide_posts( $query ) {
		$hidden_posts = $this->get_hidden_posts();
		if ( ! empty( $hidden_posts ) ) {
			$query->set( 'post__not_in', $hidden_posts );
		}
		return $query;
	}

	/**
	 * Filter to hide pages for get_pages().
	 *
	 * @since 3.2.0
	 *
	 * @global object $wpdb
	 * @param  array  $pages
	 * @return array  $pages
	 */
	function filter_get_pages( $pages ) {
		$hidden_posts = $this->get_hidden_posts();
		if ( ! empty ( $hidden_posts ) ) {
			$new_pages = array();
			foreach ( $pages as $key => $page ) {
				if ( ! in_array( $page->ID, $hidden_posts ) ) {
					$new_pages[ $key ] = $page;
				}
			}
			$pages = $new_pages;
		}
		return $pages;
	}

	/**
	 * Filter to hide menu items.
	 *
	 * @since 3.2.0
	 *
	 * @param  array  $items
	 * @param         $menu
	 * @param  array  $args
	 * @return array  $items
	 */
	function filter_nav_menu_items( $items, $menu, $args ) {
		$hidden_posts = $this->get_hidden_posts();
		if ( ! empty( $hidden_posts ) ) {
			foreach ( $items as $key => $item ) {
				if ( in_array( $item->object_id, $hidden_posts ) ) {
					unset( $items[ $key ] );
				}
			}
		}
		return $items;
	}

	/**
	 * Filter to remove hidden posts from prev/next links.
	 *
	 * @since 3.2.4
	 *
	 * @global object $wpmem
	 * @param  string $where
	 * @return string $where
	 */
	function filter_get_adjacent_post_where( $where ) {
		global $wpmem;
		$hidden_posts = $this->get_hidden_posts();
		if ( ! empty( $hidden_posts ) ) {
			$hidden = implode( ",", $hidden_posts );	
			$where  = $where . " AND p.ID NOT IN ( $hidden )";
		}
		return $where;
	}

	/**
	 * Sets the registration fields.
	 *
	 * @since 3.0.0
	 * @since 3.1.5 Added $form argument.
	 *
	 * @param string $form The form being generated.
	 */
	function load_fields( $form = 'default' ) {
		
		// Get stored fields settings.
		$fields = get_option( 'wpmembers_fields' );
		
		// Validate fields settings.
		if ( ! isset( $fields ) || empty( $fields ) ) {
			// Update settings.
			$fields = array( array( 10, 'Email', 'user_email', 'email', 'y', 'y', 'y' ) );
		}
		
		// Add new field array keys
		foreach ( $fields as $key => $val ) {
			
			// Key fields with meta key.
			$meta_key = $val[2];
			
			// Old format, new key.
			foreach ( $val as $subkey => $subval ) {
				$this->fields[ $meta_key ][ $subkey ] = $subval;
			}
			
			// Setup field properties.
			$this->fields[ $meta_key ]['label']    = $val[1];
			$this->fields[ $meta_key ]['type']     = $val[3];
			$this->fields[ $meta_key ]['register'] = ( 'y' == $val[4] ) ? true : false;
			$this->fields[ $meta_key ]['required'] = ( 'y' == $val[5] ) ? true : false;
			$this->fields[ $meta_key ]['profile']  = '';
			$this->fields[ $meta_key ]['native']   = ( 'y' == $val[6] ) ? true : false;
			
			// Certain field types have additional properties.
			switch ( $val[3] ) {
				
				case 'checkbox':
					$this->fields[ $meta_key ]['checked_value']   = $val[7];
					$this->fields[ $meta_key ]['checked_default'] = ( 'y' == $val[8] ) ? true : false;
					break;

				case 'select':
				case 'multiselect':
				case 'multicheckbox':
				case 'radio':
					// Correct a malformed value (if last value is empty due to a trailing comma).
					if ( '' == end( $val[7] ) ) {
						array_pop( $val[7] );
						$this->fields[ $meta_key ][7] = $val[7];
					}
					$this->fields[ $meta_key ]['values']    = $val[7];
					$this->fields[ $meta_key ]['delimiter'] = ( isset( $val[8] ) ) ? $val[8] : '|';
					$this->fields[ $meta_key ]['options']   = array();
					foreach ( $val[7] as $value ) {
						$pieces = explode( '|', trim( $value ) );
						if ( isset( $pieces[1] ) && $pieces[1] != '' ) {
							$this->fields[ $meta_key ]['options'][ $pieces[1] ] = $pieces[0];
						}
					}
					break;

				case 'file':
				case 'image':
					$this->fields[ $meta_key ]['file_types'] = $val[7];
					break;

				case 'hidden':
					$this->fields[ $meta_key ]['value'] = $val[7];
					break;
					
			}
		}
	}
	
	/**
	 * Get excluded meta fields.
	 *
	 * @since 3.0.0
	 *
	 * @param  string $tag A tag so we know where the function is being used.
	 * @return array       The excluded fields.
	 */
	function excluded_fields( $tag ) {

		// Default excluded fields.
		$excluded_fields = array( 'password', 'confirm_password', 'confirm_email', 'password_confirm', 'email_confirm' );
		
		if ( 'update' == $tag || 'admin-profile' == $tag || 'user-profile' == $tag || 'wp-register' == $tag ) {
			$excluded_fields[] = 'username';
		}

		if ( 'admin-profile' == $tag || 'user-profile' == $tag ) {
			array_push( $excluded_fields, 'first_name', 'last_name', 'nickname', 'display_name', 'user_email', 'description', 'user_url' );
		}

		/**
		 * Filter the fields to be excluded when user is created/updated.
		 *
		 * @since 2.9.3
		 * @since 3.0.0 Moved to new method in WP_Members Class.
		 *
		 * @param array       An array of the field meta names to exclude.
		 * @param string $tag A tag so we know where the function is being used.
		 */
		$excluded_fields = apply_filters( 'wpmem_exclude_fields', $excluded_fields, $tag );

		// Return excluded fields.
		return $excluded_fields;
	}
	
	/**
	 * Set page locations.
	 *
	 * Handles numeric page IDs while maintaining
	 * compatibility with old full url settings.
	 *
	 * @since 3.0.8
	 */
	function load_user_pages() {
		foreach ( $this->user_pages as $key => $val ) {
			if ( is_numeric( $val ) ) {
				$this->user_pages[ $key ] = get_page_link( $val );
			}
		}
	}
	
	/**
	 * Returns a requested text string.
	 *
	 * This function manages all of the front-end facing text.
	 * All defaults can be filtered using wpmem_default_text_strings.
	 *
	 * @since 3.1.0
	 *
	 * @param  string $str
	 * @return string $text
	 */	
	function get_text( $str ) {

		// Default Form Fields.
		$default_form_fields = array(
			'first_name'       => __( 'First Name', 'wp-members' ),
			'last_name'        => __( 'Last Name', 'wp-members' ),
			'addr1'            => __( 'Address 1', 'wp-members' ),
			'addr2'            => __( 'Address 2', 'wp-members' ),
			'city'             => __( 'City', 'wp-members' ),
			'thestate'         => __( 'State', 'wp-members' ),
			'zip'              => __( 'Zip', 'wp-members' ),
			'country'          => __( 'Country', 'wp-members' ),
			'phone1'           => __( 'Day Phone', 'wp-members' ),
			'user_email'       => __( 'Email', 'wp-members' ),
			'confirm_email'    => __( 'Confirm Email', 'wp-members' ),
			'user_url'         => __( 'Website', 'wp-members' ),
			'description'      => __( 'Biographical Info', 'wp-members' ),
			'password'         => __( 'Password', 'wp-members' ),
			'confirm_password' => __( 'Confirm Password', 'wp-members' ),
			'tos'              => __( 'TOS', 'wp-members' ),
		);
		
		/*
		 * Strings to be added or removed in future versions, included so they will
		 * be in the translation template.
		 * @todo Check whether any of these should be removed.
		 */
		$benign_strings = array(
			__( 'No fields selected for deletion', 'wp-members' ),
			__( 'You are not logged in.', 'wp-members' ), // Technically removed 3.5
		);
	
		$defaults = array(
			
			// Login form.
			'login_heading'        => __( 'Existing Users Log In', 'wp-members' ),
			'login_username'       => __( 'Username or Email', 'wp-members' ),
			'login_password'       => __( 'Password', 'wp-members' ),
			'login_button'         => __( 'Log In', 'wp-members' ),
			'remember_me'          => __( 'Remember Me', 'wp-members' ),
			'forgot_link_before'   => __( 'Forgot password?', 'wp-members' ) . '&nbsp;',
			'forgot_link'          => __( 'Click here to reset', 'wp-members' ),
			'register_link_before' => __( 'New User?', 'wp-members' ) . '&nbsp;',
			'register_link'        => __( 'Click here to register', 'wp-members' ),
			
			// Password change form.
			'pwdchg_heading'       => __( 'Change Password', 'wp-members' ),
			'pwdchg_password1'     => __( 'New password', 'wp-members' ),
			'pwdchg_password2'     => __( 'Confirm new password', 'wp-members' ),
			'pwdchg_button'        => __( 'Update Password', 'wp-members' ),
			
			// Password reset form.
			'pwdreset_heading'     => __( 'Reset Forgotten Password', 'wp-members' ),
			'pwdreset_username'    => __( 'Username', 'wp-members' ),
			'pwdreset_email'       => __( 'Email', 'wp-members' ),
			'pwdreset_button'      => __( 'Reset Password' ),
			'username_link_before' => __( 'Forgot username?', 'wp-members' ) . '&nbsp;',
			'username_link'        => __( 'Click here', 'wp-members' ),
			
			// Retrieve username form.
			'username_heading'     => __( 'Retrieve username', 'wp-members' ),
			'username_email'       => __( 'Email Address', 'wp-members' ),
			'username_button'      => __( 'Retrieve username', 'wp-members' ),
			
			// Register form.
			'register_heading'     => __( 'New User Registration', 'wp-members' ),
			'register_username'    => __( 'Choose a Username', 'wp-members' ),
			'register_rscaptcha'   => __( 'Input the code:', 'wp-members' ),
			'register_tos'         => __( 'Please indicate that you agree to the %s Terms of Service %s', 'wp-members' ), // @note: if default changes, default check after wpmem_tos_link_txt must change.
			'register_clear'       => __( 'Reset Form', 'wp-members' ),
			'register_submit'      => __( 'Register', 'wp-members' ),
			'register_req_mark'    => '<span class="req">*</span>',
			'register_required'    => '<span class="req">*</span>' . __( 'Required field', 'wp-members' ),
			
			// User profile update form.
			'profile_heading'      => __( 'Edit Your Information', 'wp-members' ),
			'profile_username'     => __( 'Username', 'wp-members' ),
			'profile_submit'       => __( 'Update Profile', 'wp-members' ),
			'profile_upload'       => __( 'Update this file', 'wp-members' ),
			
			// Error messages and dialogs.
			'login_failed_heading' => __( 'Login Failed!', 'wp-members' ),
			'login_failed'         => __( 'You entered an invalid username or password.', 'wp-members' ),
			'login_failed_link'    => __( 'Click here to continue.', 'wp-members' ),
			'pwdchangempty'        => __( 'Password fields cannot be empty', 'wp-members' ),
			'usernamefailed'       => __( 'Sorry, that email address was not found.', 'wp-members' ),
			'usernamesuccess'      => __( 'An email was sent to %s with your username.', 'wp-members' ),
			'reg_empty_field'      => __( 'Sorry, %s is a required field.', 'wp-members' ),
			'reg_valid_email'      => __( 'You must enter a valid email address.', 'wp-members' ),
			'reg_non_alphanumeric' => __( 'The username cannot include non-alphanumeric characters.', 'wp-members' ),
			'reg_empty_username'   => __( 'Sorry, username is a required field', 'wp-members' ),
			'reg_password_match'   => __( 'Passwords did not match.', 'wp-members' ),
			'reg_email_match'      => __( 'Emails did not match.', 'wp-members' ),
			'reg_empty_captcha'    => __( 'You must complete the CAPTCHA form.', 'wp-members' ),
			'reg_invalid_captcha'  => __( 'CAPTCHA was not valid.', 'wp-members' ),
			'reg_generic'          => __( 'There was an error processing the form.', 'wp-members' ),
			'reg_captcha_err'      => __( 'There was an error with the CAPTCHA form.', 'wp-members' ),
			
			// Links.
			'profile_edit'         => __( 'Edit My Information', 'wp-members' ),
			'profile_password'     => __( 'Change Password', 'wp-members' ),
			'register_status'      => __( 'You are logged in as %s', 'wp-members' ),
			'register_logout'      => __( 'Click to log out.', 'wp-members' ),
			'register_continue'    => __( 'Begin using the site.', 'wp-members' ),
			'login_welcome'        => __( 'You are logged in as %s', 'wp-members' ),
			'login_logout'         => __( 'Click to log out', 'wp-members' ),
			'status_welcome'       => __( 'You are logged in as %s', 'wp-members' ),
			'status_logout'        => __( 'click to log out', 'wp-members' ),
			'menu_logout'          => __( 'Log Out', 'wp-members' ),
			
			// Widget.
			'sb_status'            => __( 'You are logged in as %s', 'wp-members' ),
			'sb_logout'            => __( 'click here to log out', 'wp-members' ),
			'sb_login_failed'      => __( 'Login Failed!<br />You entered an invalid username or password.', 'wp-members' ),
			'sb_not_logged_in'     => '',
			'sb_login_username'    => __( 'Username or Email', 'wp-members' ),
			'sb_login_password'    => __( 'Password', 'wp-members' ),
			'sb_login_button'      => __( 'log in', 'wp-members' ),
			'sb_login_forgot'      => __( 'Forgot?', 'wp-members' ),
			'sb_login_register'    => __( 'Register', 'wp-members' ),
			
			// Default Dialogs.
			'restricted_msg'       => __( "This content is restricted to site members.  If you are an existing user, please log in.  New users may register below.", 'wp-members' ),
			'success'              => __( "Congratulations! Your registration was successful.<br /><br />You may now log in using the password that was emailed to you.", 'wp-members' ),
			
			// @todo Under consideration for removal from the Dialogs tab.
			'user'                 => __( "Sorry, that username is taken, please try another.", 'wp-members' ),
			'email'                => __( "Sorry, that email address already has an account.<br />Please try another.", 'wp-members' ),
			'editsuccess'          => __( "Your information was updated!", 'wp-members' ),
			
			// @todo These are defaults and are under consideration for removal from the dialogs tab, possibly as we change the password reset to a link based process.
			'pwdchangerr'          => __( "Passwords did not match.<br /><br />Please try again.", 'wp-members' ),
			'pwdchangesuccess'     => __( "Password successfully changed!", 'wp-members' ),
			'pwdreseterr'          => __( "Either the username or email address do not exist in our records.", 'wp-members' ),
			'pwdresetsuccess'      => __( "Password successfully reset!<br /><br />An email containing a new password has been sent to the email address on file for your account.", 'wp-members' ),
			
			'product_restricted'   => __( "Sorry, you do not have access to this content.", 'wp-members' ),
		
		); // End of $defaults array.
		
		/**
		 * Filter default terms.
		 *
		 * @since 3.1.0
		 * @deprecated 3.2.7 Use wpmem_default_text instead.
		 */
		$text = apply_filters( 'wpmem_default_text_strings', '' );
		
		// Merge filtered $terms with $defaults.
		$text = wp_parse_args( $text, $defaults );
		
		/**
		 * Filter the default terms.
		 *
		 * Replaces 'wpmem_default_text_strings' so that multiple filters could
		 * be run. This allows for custom filters when also running the Text
		 * String Editor extension.
		 *
		 * @since 3.2.7
		 */
		$text = apply_filters( 'wpmem_default_text', $text );
		
		// Return the requested text string.
		return $text[ $str ];
	
	} // End of get_text().
	
	/**
	 * Load the admin api.
	 *
	 * @since 3.1.0
	 */
	function load_admin_api() {
		if ( is_admin() ) {
			/**
			 * Load the admin api class.
			 *
			 * @since 3.1.0
			 */	
			include_once( WPMEM_PATH . 'admin/includes/class-wp-members-admin-api.php' );
			$this->admin = new WP_Members_Admin_API;
		}
	}
	
	/**
	 * Initializes the WP-Members widget.
	 *
	 * @since 3.2.0 Replaces widget_wpmemwidget_init
	 */
	public function widget_init() {
		// Register the WP-Members widget.
		register_widget( 'widget_wpmemwidget' );
	}
	
	/**
	 * Adds WP-Members query vars to WP's public query vars.
	 *
	 * @since 3.2.0
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 *
	 * @param	array	$qvars
	 */
	public function add_query_vars ( $qvars ) {
		$qvars[] = 'a'; // The WP-Members action variable.
		return $qvars;
	}
	
	/**
	 * Enqueues login/out script for the footer.
	 *
	 * @since 3.2.0
	 */
	public function loginout_script() {
		if ( is_user_logged_in() ) {
			wp_enqueue_script( 'jquery' );
			add_action( 'wp_footer', array( $this, 'do_loginout_script' ), 50 );
		}
	}
	
	/**
	 * Outputs login/out script for the footer.
	 *
	 * @since 3.2.0
	 *
	 * @global object $wpmem
	 */
	public function do_loginout_script() {
		global $wpmem;
		$logout = apply_filters( 'wpmem_logout_link', add_query_arg( 'a', 'logout' ) );
		?><script type="text/javascript">
			jQuery('.wpmem_loginout').html('<a class="login_button" href="<?php echo $logout; ?>"><?php echo $this->get_text( 'menu_logout' ); ?></a>');
		</script><?php
	}
		
	/**
	 * Adds WP-Members controls to the Customizer
	 *
	 * @since 3.2.0
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function customizer_settings( $wp_customize ) {
		$wp_customize->add_section( 'wp_members' , array(
			'title'      => 'WP-Members',
			'priority'   => 190,
		) );

		// Add settings for output description
		$wp_customize->add_setting( 'wpmem_show_logged_out_state', array(
			'default'    => '1',
			'type'       => 'theme_mod', //'option'
			'capability' => 'edit_theme_options',
			'transport'  => 'refresh',
		) );

		// Add settings for output description
		$wp_customize->add_setting( 'wpmem_show_form_message_dialog', array(
			'default'    => '1',
			'type'       => 'theme_mod', //'option'
			'capability' => 'edit_theme_options',
			'transport'  => 'refresh',
		) );

		// Add control and output for select field
		$wp_customize->add_control( 'wpmem_show_form_logged_out', array(
			'label'      => __( 'Show forms as logged out', 'wp-members' ),
			'section'    => 'wp_members',
			'settings'   => 'wpmem_show_logged_out_state',
			'type'       => 'checkbox',
			'std'        => '1'
		) );
		
		// Add control for showing dialog
		$wp_customize->add_control( 'wpmem_show_form_dialog', array(
			'label'      => __( 'Show form message dialog', 'wp-members' ),
			'section'    => 'wp_members',
			'settings'   => 'wpmem_show_form_message_dialog',
			'type'       => 'checkbox',
			'std'        => '0'
		) );
	}

	/**
	 * Overrides the wptexturize filter.
	 *
	 * Currently only used for the login form to remove the <br> tag that WP puts in after the "Remember Me".
	 *
	 * @since 2.6.4
	 * @since 3.2.3 Moved to WP_Members class.
	 *
	 * @todo Possibly deprecate or severely alter this process as its need may be obsolete.
	 *
	 * @param  string $content
	 * @return string $new_content
	 */
	function texturize( $content ) {

		$new_content = '';
		$pattern_full = '{(\[wpmem_txt\].*?\[/wpmem_txt\])}is';
		$pattern_contents = '{\[wpmem_txt\](.*?)\[/wpmem_txt\]}is';
		$pieces = preg_split( $pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE );

		foreach ( $pieces as $piece ) {
			if ( preg_match( $pattern_contents, $piece, $matches ) ) {
				$new_content .= $matches[1];
			} else {
				$new_content .= wptexturize( wpautop( $piece ) );
			}
		}

		return $new_content;
	}
	
	/**
	 * Loads the stylesheet for tableless forms.
	 *
	 * @since 2.6
	 * @since 3.2.3 Moved to WP_Members class.
	 *
	 * @global object $wpmem The WP_Members object. 
	 */
	function enqueue_style() {
		global $wpmem;
		wp_enqueue_style ( 'wp-members', wpmem_force_ssl( $wpmem->cssurl ), '', WPMEM_VERSION );
	}

	/**
	 * Creates an excerpt on the fly if there is no 'more' tag.
	 *
	 * @since 2.6
	 * @since 3.2.3 Moved to WP_Members class.
	 * @since 3.2.5 Check if post object exists.
	 *
	 * @global object $post  The post object.
	 * @global object $wpmem The WP_Members object.
	 *
	 * @param  string $content
	 * @return string $content
	 */
	function do_excerpt( $content ) {

		global $post, $more, $wpmem;
		
		if ( is_object( $post ) ) {
			
			$post_id   = $post->ID;
			$post_type = $post->post_type;

			$autoex = ( isset( $wpmem->autoex[ $post->post_type ] ) && 1 == $wpmem->autoex[ $post->post_type ]['enabled'] ) ? $wpmem->autoex[ $post->post_type ] : false;

			// Is there already a 'more' link in the content?
			$has_more_link = ( stristr( $content, 'class="more-link"' ) ) ? true : false;

			// If auto_ex is on.
			if ( $autoex ) {

				// Build an excerpt if one does not exist.
				if ( ! $has_more_link ) {

					$is_singular = ( is_singular( $post->post_type ) ) ? true : false;

					if ( $is_singular ) {
						// If it's a single post, we don't need the 'more' link.
						$more_link_text = '';
						$more_link      = '';
					} else {
						// The default $more_link_text.
						if ( isset( $wpmem->autoex[ $post->post_type ]['text'] ) && '' != $wpmem->autoex[ $post->post_type ]['text'] ) {
							$more_link_text = __( $wpmem->autoex[ $post->post_type ]['text'], 'wp-members' );
						} else {
							$more_link_text = __( '(more&hellip;)' );
						}
						// The default $more_link.
						$more_link = ' <a href="'. get_permalink( $post->ID ) . '" class="more-link">' . $more_link_text . '</a>';
					}

					// Apply the_content_more_link filter if one exists (will match up all 'more' link text).
					/** This filter is documented in /wp-includes/post-template.php */
					$more_link = apply_filters( 'the_content_more_link', $more_link, $more_link_text );

					$defaults = array(
						'length'           => $autoex['length'],
						'more_link'        => $more_link,
						'blocked_only'     => false,
					);
					/**
					 * Filter auto excerpt defaults.
					 *
					 * @since 3.0.9
					 * @since 3.1.5 Deprecated add_ellipsis, strip_tags, close_tags, parse_shortcodes, strip_shortcodes.
					 *
					 * @param array {
					 *     An array of settings to override the function defaults.
					 *
					 *     @type int         $length           The default length of the excerpt.
					 *     @type string      $more_link        The more link HTML.
					 *     @type boolean     $blocked_only     Run autoexcerpt only on blocked content. default: false.
					 * }
					 * @param string $post->ID        The post ID.
					 * @param string $post->post_type The content's post type.					 
					 */
					$args = apply_filters( 'wpmem_auto_excerpt_args', '', $post->ID, $post->post_type );

					// Merge settings.
					$args = wp_parse_args( $args, $defaults );

					// Are we only excerpting blocked content?
					if ( $args['blocked_only'] ) {
						$post_meta = get_post_meta( $post->ID, '_wpmem_block', true );
						if ( 1 == $wpmem->block[ $post->post_type ] ) {
							// Post type is blocked, if post meta unblocks it, don't do excerpt.
							$do_excerpt = ( "0" == $post_meta ) ? false : true;
						} else {
							// Post type is unblocked, if post meta blocks it, do excerpt.
							$do_excerpt = ( "1" == $post_meta ) ? true : false;
						} 
					} else {
						$do_excerpt = true;
					}

					if ( $do_excerpt ) {
						$content = wp_trim_words( $content, $args['length'], $args['more_link'] );
						// Check if the more link was added (note: singular has no more_link):
						if ( ! $is_singular && ! strpos( $content, $args['more_link'] ) ) {
							$content = $content . $args['more_link'];
						}
					}
				}
			}
		} else {
			$post_id   = false;
			$post_type = false;
		}

		/**
		 * Filter the auto excerpt.
		 *
		 * @since 2.8.1
		 * @since 3.0.9 Added post ID and post type parameters.
		 * @since 3.2.5 Post ID and post type may be false if there is no post object.
		 * 
		 * @param string $content   The content excerpt.
		 * @param string $post_id   The post ID.
		 * @param string $post_type The content's post type.
		 */
		$content = apply_filters( 'wpmem_auto_excerpt', $content, $post_id, $post_type );

		// Return the excerpt.
		return $content;
	}

	/**
	 * Convert form tag.
	 *
	 * @todo This is temporary to handle form tag conversion.
	 *
	 * @since 3.1.7
	 * @since 3.2.3 Moved to WP_Members class.
	 *
	 * @param  string $tag
	 * @return string $tag
	 */
	function convert_tag( $tag ) {
		switch ( $tag ) {
			case 'new':
				return 'register';
				break;
			case 'edit':
			case 'update':
				return 'profile';
				break;
			case 'wp':
			case 'wp_validate':
			case 'wp_finalize':
				return 'register_wp';
				break;
			case 'dashboard_profile':
			case 'dashboard_profile_update':
				return 'profile_dashboard';
				break;
			case 'admin_profile':
			case 'admin_profile_update':
				return 'profile_admin';
				break;
			default:
				return $tag;
				break;
		}
		return $tag;
	}

	/**
	 * Loads translation files.
	 *
	 * @since 3.0.0
	 * @since 3.2.5 Moved to main object, dropped wpmem_ stem.
	 */
	function load_textdomain() {

		// @see: https://ulrich.pogson.ch/load-theme-plugin-translations for notes on changes.

		// Plugin textdomain.
		$domain = 'wp-members';

		// Wordpress locale.
		/** This filter is documented in wp-includes/l10n.php */
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		/**
		 * Filter translation file.
		 *
		 * If the translate.wordpress.org language pack is available, it will
		 * be /wp-content/languages/plugins/wp-members-{locale}.mo by default.
		 * You can filter this if you want to load a language pack from a
		 * different location (or different file name).
		 *
		 * @since 3.0.0
		 * @since 3.2.0 Added locale as a parameter.
		 *
		 * @param string $file   The translation file to load.
		 * @param string $locale The current locale.
		 */
		$file = apply_filters( 'wpmem_localization_file', trailingslashit( WP_LANG_DIR ) . 'plugins/' . $domain . '-' . $locale . '.mo', $locale );

		$loaded = load_textdomain( $domain, $file );
		if ( $loaded ) {
			return $loaded;
		} else {

			/*
			 * If there is no wordpress.org language pack or the filtered
			 * language file does not load, $loaded will be false and will
			 * end up here to attempt to load one of the legacy language
			 * packs. Note that the legacy language files are no longer
			 * actively maintained and may not contain all strings.
			 * The directory that the file will load from can be changed
			 * using the wpmem_localization_dir filter.
			 */

			/**
			 * Filter translation directory.
			 *
			 * @since 3.0.3
			 * @since 3.2.0 Added locale as a parameter.
			 *
			 * @param string $dir    The translation directory.
			 * @param string $locale The current locale.
			 */
			$dir = apply_filters( 'wpmem_localization_dir', dirname( plugin_basename( __FILE__ ) ) . '/lang/', $locale );
			load_plugin_textdomain( $domain, FALSE, $dir );
		}
		return;
	}

} // End of WP_Members class.