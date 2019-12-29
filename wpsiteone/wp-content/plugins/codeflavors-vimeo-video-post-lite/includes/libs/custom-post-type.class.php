<?php

/**
 * Custom post type class.
 * Manages post registering, taxonomies, data saving
 */
class CVM_Video_Post_Type{
	/**
	 * @var string
	 */
	private $post_type = 'vimeo-video';
	/**
	 * @var string
	 */
	private $taxonomy = 'vimeo-videos';
	/**
	 * @var string
	 */
	private $tag = 'vimeo-tag';
	/**
	 * @var array
	 */
	private $help_screens = array();
	/**
	 * @var string
	 */
	private $ajax_import_action = 'cvm_import_videos';
	/**
	 * @var string
	 */
	private $ajax_video_query_action = 'cvm_query_video';
	/**
	 * @var string
	 */
	private $ajax_new_category_action = 'cvm-create-video-category';
	/**
	 * @var bool
	 */
	private $is_gutenberg = false;

	/**
	 * CVM_Video_Post_Type constructor.
	 */
	public function __construct(){
		// start REST API compatibility
		new CVM_REST_API( $this );
		
		// custom post type registration and messages
		add_action( 'init', array( 
				$this, 
				'register_post' 
		), 10 );
		add_filter( 'post_updated_messages', array( 
				$this, 
				'updated_messages' 
		) );
		// for empty imported posts, skip $maybe_empty verification
		add_filter( 'wp_insert_post_empty_content', array( 
				$this, 
				'force_empty_insert' 
		), 999, 2 );
		// create edit meta boxes
		add_action( 'admin_init', array( 
				$this, 
				'add_meta_boxes' 
		) );
		// add privacy information
		add_action( 'admin_init', array(
			$this,
			'privacy_policy'
		) );
		// post thumbnails
		add_filter( 'admin_post_thumbnail_html', array(
			$this,
			'post_thumbnail_meta_panel'
		), 10, 2 );
		// save data from meta boxes
		add_action( 'save_post', array( 
				$this, 
				'save_post' 
		), 10, 2 );
		
		// add extra menu pages
		add_action( 'admin_menu', array( 
				$this, 
				'menu_pages' 
		), 1 );
		
		// add columns to posts table
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( 
				$this, 
				'extra_columns' 
		) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( 
				$this, 
				'output_extra_columns' 
		), 10, 2 );
		
		// response to ajax import
		add_action( 'wp_ajax_' . $this->ajax_import_action, array( 
				$this, 
				'ajax_import_videos' 
		) );
		// response to new video ajax query
		add_action( 'wp_ajax_' . $this->ajax_video_query_action, array( 
				$this, 
				'ajax_video_query' 
		) );
		// new category ajax response
		add_action( 'wp_ajax_' . $this->ajax_new_category_action, array( 
				$this, 
				'ajax_new_category' 
		) );
		// post edit screen import vidoe thumbnail
		add_action( 'wp_ajax_cvm_import_video_thumbnail', array(
		        $this,
                'import_thumbnail'
        ) );

		add_action( 'load-post-new.php', array( 
				$this, 
				'post_new_onload' 
		) );
		add_action( 'admin_enqueue_scripts', array( 
				$this, 
				'post_edit_assets' 
		) );
		
		// help screens
		add_filter( 'contextual_help', array( 
				$this, 
				'contextual_help' 
		), 10, 3 );
		
		// add a filter to detect if PRO version is installed and remove its activation link and add a message
		add_filter( 'plugin_row_meta', array( 
				$this, 
				'plugin_meta' 
		), 10, 2 );
		add_filter( 'plugin_action_links', array( 
				$this, 
				'plugin_action_links' 
		), 10, 2 );

		add_action( 'admin_init', array(
			$this,
			'activation_redirect'
		) );

		// Gutenberg action
		add_action( 'enqueue_block_editor_assets', array(
			$this,
			'gutenberg_editor'
		) );
	}

	/**
	 * Register video post type and taxonomies
	 */
	public function register_post(){
		$labels = array( 
				'name' => _x( 'Videos', 'Videos', 'codeflavors-vimeo-video-post-lite' ), 
				'singular_name' => _x( 'Video', 'Video', 'codeflavors-vimeo-video-post-lite' ), 
				'add_new' => _x( 'Add new', 'video', 'codeflavors-vimeo-video-post-lite' ), 
				'add_new_item' => __( 'Add new video', 'codeflavors-vimeo-video-post-lite' ), 
				'edit_item' => __( 'Edit video', 'codeflavors-vimeo-video-post-lite' ), 
				'new_item' => __( 'New video', 'codeflavors-vimeo-video-post-lite' ), 
				'all_items' => __( 'All videos', 'codeflavors-vimeo-video-post-lite' ), 
				'view_item' => __( 'View', 'codeflavors-vimeo-video-post-lite' ), 
				'search_items' => __( 'Search', 'codeflavors-vimeo-video-post-lite' ), 
				'not_found' => __( 'No videos found', 'codeflavors-vimeo-video-post-lite' ), 
				'not_found_in_trash' => __( 'No videos in trash', 'codeflavors-vimeo-video-post-lite' ), 
				'parent_item_colon' => '', 
				'menu_name' => __( 'Videos', 'codeflavors-vimeo-video-post-lite' ) 
		);
		
		$options = cvm_get_settings();
		$is_public = $options[ 'public' ];
		
		$args = array( 
				'labels' => $labels, 
				'public' => $is_public, 
				'exclude_from_search' => ! $is_public, 
				'publicly_queryable' => $is_public, 
				'show_in_nav_menus' => $is_public, 
				
				'show_ui' => true, 
				'show_in_menu' => true, 
				'menu_position' => 5, 
				'menu_icon' => CVM_URL . 'assets/back-end/images/video.png', 
				
				'query_var' => true, 
				'capability_type' => 'post', 
				'has_archive' => $is_public, 
				'hierarchical' => false, 
				
				// REST support
				'show_in_rest' => true, 
				
				'rewrite' => array( 
						'slug' => $this->post_type 
				), 
				
				'supports' => array( 
						'title', 
						'editor', 
						'author', 
						'thumbnail', 
						'excerpt', 
						'trackbacks', 
						'custom-fields', 
						'comments', 
						'revisions', 
						'post-formats' 
				) 
		);
		
		register_post_type( $this->post_type, $args );
		
		// Add new taxonomy, make it hierarchical (like categories)
		$cat_labels = array( 
				'name' => _x( 'Video categories', 'Video categories', 'codeflavors-vimeo-video-post-lite' ), 
				'singular_name' => _x( 'Video category', 'Video category', 'codeflavors-vimeo-video-post-lite' ), 
				'search_items' => __( 'Search video category', 'codeflavors-vimeo-video-post-lite' ), 
				'all_items' => __( 'All video categories', 'codeflavors-vimeo-video-post-lite' ), 
				'parent_item' => __( 'Video category parent', 'codeflavors-vimeo-video-post-lite' ), 
				'parent_item_colon' => __( 'Video category parent:', 'codeflavors-vimeo-video-post-lite' ), 
				'edit_item' => __( 'Edit video category', 'codeflavors-vimeo-video-post-lite' ), 
				'update_item' => __( 'Update video category', 'codeflavors-vimeo-video-post-lite' ), 
				'add_new_item' => __( 'Add new video category', 'codeflavors-vimeo-video-post-lite' ), 
				'new_item_name' => __( 'Video category name', 'codeflavors-vimeo-video-post-lite' ), 
				'menu_name' => __( 'Video categories', 'codeflavors-vimeo-video-post-lite' ) 
		);
		
		register_taxonomy( $this->taxonomy, array( 
				$this->post_type 
		), array( 
				'public' => $is_public, 
				'show_ui' => true, 
				'show_in_nav_menus' => $is_public, 
				'show_admin_column' => true, 
				'hierarchical' => true, 
				// REST support
				'show_in_rest' => true, 
				'rewrite' => array( 
						'slug' => 'videos' 
				), 
				'capabilities' => array( 
						'edit_posts' 
				), 
				'labels' => $cat_labels, 
				'query_var' => true 
		) );
		
		// tags
		$tag_labels = array( 
				'name' => _x( 'Vimeo Video tags', 'Vimeo Video tags', 'codeflavors-vimeo-video-post-lite' ), 
				'singular_name' => _x( 'Vimeo Video tag', 'Vimeo Video tag', 'codeflavors-vimeo-video-post-lite' ), 
				'search_items' => __( 'Search video tag', 'codeflavors-vimeo-video-post-lite' ), 
				'all_items' => __( 'All video tags', 'codeflavors-vimeo-video-post-lite' ), 
				'parent_item' => __( 'Video tag parent', 'codeflavors-vimeo-video-post-lite' ), 
				'parent_item_colon' => __( 'Video tag parent:', 'codeflavors-vimeo-video-post-lite' ), 
				'edit_item' => __( 'Edit video tag', 'codeflavors-vimeo-video-post-lite' ), 
				'update_item' => __( 'Update video tag', 'codeflavors-vimeo-video-post-lite' ), 
				'add_new_item' => __( 'Add new video tag', 'codeflavors-vimeo-video-post-lite' ), 
				'new_item_name' => __( 'Video tag name', 'codeflavors-vimeo-video-post-lite' ), 
				'menu_name' => __( 'Tags', 'codeflavors-vimeo-video-post-lite' ) 
		);
		register_taxonomy( $this->tag, array( 
				$this->post_type 
		), array( 
				'public' => $is_public, 
				'show_ui' => true, 
				'show_in_nav_menus' => $is_public, 
				'show_admin_column' => true, 
				'hierarchical' => false, 
				// REST support
				'show_in_rest' => true, 
				'rewrite' => array( 
						'slug' => 'vimeo-tag' 
				), 
				'capabilities' => array( 
						'edit_posts' 
				), 
				'labels' => $tag_labels, 
				'query_var' => true 
		) );
	}

	/**
	 * Custom post type messages on edit, update, create, etc.
	 * 
	 * @param array $messages
	 */
	public function updated_messages( $messages ){
		global $post, $post_ID;
		
		if( ! $post || $this->post_type !== $post->post_type ){
			return;
		}
		
		$vid_id = isset( $_GET[ 'video_id' ] ) ? $_GET[ 'video_id' ] : '';
		
		$messages[ $this->post_type ] = array( 
				0 => '',  // Unused. Messages start at index 1.
				1 => sprintf( __( 'Video updated <a href="%s">See video</a>', 'codeflavors-vimeo-video-post-lite' ), esc_url( get_permalink( $post_ID ) ) ), 
				2 => __( 'Custom field updated.', 'codeflavors-vimeo-video-post-lite' ), 
				3 => __( 'Custom field deleted.', 'codeflavors-vimeo-video-post-lite' ), 
				4 => __( 'Video updated.', 'codeflavors-vimeo-video-post-lite' ),
	   		/* translators: %s: date and time of the revision */
	    	5 => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Video restored to version %s', 'codeflavors-vimeo-video-post-lite' ), wp_post_revision_title( ( int ) $_GET[ 'revision' ], false ) ) : false, 
				6 => sprintf( __( 'Video published. <a href="%s">See video</a>', 'codeflavors-vimeo-video-post-lite' ), esc_url( get_permalink( $post_ID ) ) ), 
				7 => __( 'Video saved.', 'codeflavors-vimeo-video-post-lite' ), 
				8 => sprintf( __( 'Video saved. <a target="_blank" href="%s">See video</a>', 'codeflavors-vimeo-video-post-lite' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ), 
				9 => sprintf( __( 'Video will be published at: <strong>%1$s</strong>. <a target="_blank" href="%2$s">See video</a>', 'codeflavors-vimeo-video-post-lite' ), 
						// translators: Publish box date format, see http://php.net/date
						date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ), 
				10 => sprintf( __( 'Video draft saved. <a target="_blank" href="%s">See video</a>', 'codeflavors-vimeo-video-post-lite' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ), 
				
				101 => __( 'Please select a source', 'codeflavors-vimeo-video-post-lite' ), 
				102 => sprintf( __( 'Vimeo video with ID <strong><em>%s</em></strong> is already imported. You are now editing the existing video.', 'codeflavors-vimeo-video-post-lite' ), $vid_id ) 
		);
		
		return $messages;
	}

	/**
	 * Add subpages on our custom post type
	 */
	public function menu_pages(){
		$video_import = add_submenu_page(
		        'edit.php?post_type=' . $this->post_type,
                __( 'Import videos', 'codeflavors-vimeo-video-post-lite' ),
                __( 'Import videos', 'codeflavors-vimeo-video-post-lite' ),
                'edit_posts',
                'cvm_import',
                array(
				    $this,
				    'import_page'
		        )
        );
		add_action( 'load-' . $video_import, array( $this, 'video_import_onload' ));

		$automatic_import = add_submenu_page(
		        'edit.php?post_type=' . $this->post_type,
                __( 'Automatic Vimeo video import', 'codeflavors-vimeo-video-post-lite' ),
                __( 'Automatic import<sup>PRO</sup>', 'codeflavors-vimeo-video-post-lite' ),
                'edit_posts',
                'cvm_auto_import',
                array(
				    $this,
				    'automatic_import_page'
		        )
        );
		add_action( 'load-' . $automatic_import, array( $this, 'automatic_import_page_onload' ) );

		$settings = add_submenu_page(
		        'edit.php?post_type=' . $this->post_type,
                __( 'Settings', 'codeflavors-vimeo-video-post-lite' ),
                __( 'Settings', 'codeflavors-vimeo-video-post-lite' ),
                'manage_options',
                'cvm_settings',
                array(
				    $this,
				    'plugin_settings'
		        )
        );
		add_action( 'load-' . $settings, array( $this, 'plugin_settings_onload' ) );

		$go_pro = add_submenu_page(
			'edit.php?post_type=' . $this->post_type,
            __('Go PRO!', 'codeflavors-vimeo-video-post-lite'),
			__('Go PRO!', 'codeflavors-vimeo-video-post-lite'),
            'manage_options',
            'cvm_gopro',
            array( $this, 'go_pro_page' )
        );
		add_action( 'load-' . $go_pro, array( $this, 'go_pro_page_onload' ) );

		$videos_list = add_submenu_page(
		        null,
                __( 'Videos', 'codeflavors-vimeo-video-post-lite' ),
                __( 'Videos', 'codeflavors-vimeo-video-post-lite' ),
                'edit_posts',
                'cvm_videos',
                array(
				    $this,
				    'videos_list'
		        )
        );
		add_action( 'load-' . $videos_list, array( $this, 'video_list_onload' ) );

		$about = add_submenu_page(
		        null,
                __( 'About', 'codeflavors-vimeo-video-post-lite' ),
                __( 'About', 'codeflavors-vimeo-video-post-lite' ),
                'activate_plugins',
                'cvm_about',
                array(
                        $this,
                        'about_page'
                )
        );
		add_action( 'load-' . $about, array( $this, 'about_page_onload' ) );
	}

	/**
	 * Display contextual help on plugin pages
	 */
	public function contextual_help( $contextual_help, $screen_id, $screen ){
		// if not hooks page, return default contextual help
		if( ! is_array( $this->help_screens ) || ! array_key_exists( $screen_id, $this->help_screens ) ){
			return $contextual_help;
		}
		
		// current screen help screens
		$help_screens = $this->help_screens[ $screen_id ];
		
		// create help tabs
		foreach( $help_screens as $help_screen ){
			$screen->add_help_tab( $help_screen );
		}
	}

	/**
	 * Add meta description to plugin row in plugins page
	 * 
	 * @param array $meta
	 * @param string $file
	 */
	public function plugin_meta( $meta, $file ){
		// check if Lite is installed and disable activate link
		$pro_file = 'vimeo-video-post/main.php';
		
		if( $file == $pro_file ){
			$meta[] = '<span class="file-error">' . __( 'To activate Vimeotheque PRO you must first deactivate <strong>Vimeotheque Lite</strong>.', 'codeflavors-vimeo-video-post-lite' ) . '</span>';
		}
		return $meta;
	}

	/**
	 * Add extra actions links to plugin row in plugins page
	 * 
	 * @param array $links
	 * @param string $file
	 */
	public function plugin_action_links( $links, $file ){
		// add Settings link to plugin actions
		$plugin_file = plugin_basename( CVM_PATH . '/main.php' );
		if( $file == $plugin_file ){
			$links[] = sprintf( '<a href="%s">%s</a>', menu_page_url( 'cvm_settings', false ), __( 'Settings', 'codeflavors-vimeo-video-post-lite' ) );
		}
		
		// check if Lite is installed and disable activate link
		$pro_file = 'vimeo-video-post/main.php';
		if( $file == $pro_file ){
			unset( $links[ 'activate' ] );
		}
		
		return $links;
	}

	/**
	 * Automatic import page output
	 */
	public function automatic_import_page(){
	    include_once CVM_PATH . 'views/automatic_import.php';
	}

	/**
	 * Automatic import page load action callback
	 */
	public function automatic_import_page_onload(){
	    remove_all_actions( 'admin_notices' );
	    cvm_enqueue_player();
    }

	/**
	 * Video list is a modal page used for various actions that implie using videos.
	 * Should have no header and should be set as iframe.
	 */
	function video_list_onload(){
		$_GET[ 'noheader' ] = 'true';
		if( ! defined( 'IFRAME_REQUEST' ) ){
			define( 'IFRAME_REQUEST', true );
		}
		
		if( isset( $_GET[ '_wp_http_referer' ] ) ){
			wp_redirect( remove_query_arg( array( 
					'_wp_http_referer', 
					'_wpnonce', 
					'volume', 
					'width', 
					'aspect_ratio', 
					'autoplay', 
					'controls', 
					'cvm_video', 
					'filter_videos' 
			), stripslashes( $_SERVER[ 'REQUEST_URI' ] ) ) );
		}
	}

	/**
	 * Video list output
	 */
	function videos_list(){
		_wp_admin_html_begin();
		printf( '<title>%s</title>', __( 'Video list', 'codeflavors-vimeo-video-post-lite' ) );
		wp_enqueue_style( 'colors' );
		wp_enqueue_style( 'ie' );
		wp_enqueue_script( 'utils' );
		
		wp_enqueue_style( 'cvm-video-list-modal', CVM_URL . 'assets/back-end/css/video-list-modal.css', false, '1.0' );
		
		wp_enqueue_script( 'cvm-video-list-modal', CVM_URL . 'assets/back-end/js/video-list-modal.js', array( 
				'jquery' 
		), '1.0' );
		
		do_action( 'admin_print_styles' );
		do_action( 'admin_print_scripts' );
		do_action( 'cvm_video_list_modal_print_scripts' );
		echo '</head>';
		echo '<body>';
		
		require CVM_PATH . 'includes/libs/video-list-table.class.php';
		$table = new CVM_Video_List_Table();
		$table->prepare_items();
		
		global $CVM_POST_TYPE;
		?>
<div class="wrap">
	<form method="get" action="" id="cvm-video-list-form">
		<input type="hidden" name="post_type"
			value="<?php echo $CVM_POST_TYPE->get_post_type();?>" /> <input
			type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>" />
				<?php $table->search_box( __('Search', 'codeflavors-vimeo-video-post-lite'), 'video' );?>
				<?php $table->display();?>
			</form>
	<div id="cvm-shortcode-atts"></div>
</div>
<?php
		
		echo '</body>';
		echo '</html>';
		die();
	}

	/**
	 * Extra columns in list table
	 * 
	 * @param array $columns
	 */
	public function extra_columns( $columns ){
		$cols = array();
		foreach( $columns as $c => $t ){
			$cols[ $c ] = $t;
			if( 'title' == $c ){
				$cols[ 'video_id' ] = __( 'Video ID', 'codeflavors-vimeo-video-post-lite' );
				$cols[ 'duration' ] = __( 'Duration', 'codeflavors-vimeo-video-post-lite' );
			}
		}
		return $cols;
	}

	/**
	 * Extra columns in list table output
	 * 
	 * @param string $column_name
	 * @param int $post_id
	 */
	public function output_extra_columns( $column_name, $post_id ){
		switch( $column_name ){
			case 'video_id':
				echo get_post_meta( $post_id, '__cvm_video_id', true );
			break;
			case 'duration':
				$meta = get_post_meta( $post_id, '__cvm_video_data', true );
				echo cvm_human_time( $meta[ 'duration' ] );
			break;
		}
	}

	/**
	 * Output video importing page
	 */
	public function import_page(){
		global $CVM_List_Table;
		global $CVM_IMPORT_ERR;
		?>
<div class="wrap">
	<div class="icon32 icon32-posts-video" id="icon-edit">
		<br>
	</div>
	<h2><?php _e('Import videos', 'codeflavors-vimeo-video-post-lite')?></h2>
		<?php
		if( ! $CVM_List_Table ){
			require_once CVM_PATH . 'views/import_videos.php';
		}else{
			$CVM_List_Table->prepare_items();
			$screen = get_current_screen();
			$page_hook = $screen->id;
			
			// meta boxes
			add_meta_box( 'cvm-import-feed-entries', __( 'Import', 'codeflavors-vimeo-video-post-lite' ), array( 
					$this, 
					'import_entries_meta' 
			), $page_hook, 'side' );
			add_meta_box( 'cvm-import-new-category-metabox', __( 'Categories', 'codeflavors-vimeo-video-post-lite' ), array( 
					$this, 
					'import_categories_meta' 
			), $page_hook, 'side' );
			?>
		<?php cvm_import_errors( $CVM_List_Table->get_errors() );?>	
		<div id="poststuff" class="metabox-holder has-right-sidebar">
        	<?php
			$metabox = true;
			include CVM_PATH . 'views/import_videos.php';
			?>
        	
        	<form method="post" action="" class="ajax-submit">
			<div id="side-info-column" class="inner-sidebar">
                <?php do_meta_boxes( $page_hook, 'side', null);?>
                <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );?>
            </div>
			<div class="post-body" id="post-body">
				<div id="post-body-content">					
					<?php wp_nonce_field('cvm-import-videos-to-wp', 'cvm_import_nonce');?>
					<input type="hidden" name="action" class="cvm_ajax_action"
						value="<?php echo $this->ajax_import_action?>" /> <input
						type="hidden" name="cvm_source" value="vimeo" />
					<?php $CVM_List_Table->display();?>
				</div>
			</div>
		</form>
	</div>	
		<?php
		}
		?>
</div>
<?php
	}

	public function import_entries_meta(){
		$options = cvm_get_settings();
		?>
<label for="import_description"><?php _e('Set description as', 'codeflavors-vimeo-video-post-lite')?>:</label>
<?php
		$args = array( 
				'options' => array( 
						'content' => __( 'content', 'codeflavors-vimeo-video-post-lite' ), 
						'excerpt' => __( 'excerpt', 'codeflavors-vimeo-video-post-lite' ), 
						'content_excerpt' => __( 'both', 'codeflavors-vimeo-video-post-lite' ), 
						'none' => __( 'none', 'codeflavors-vimeo-video-post-lite' ) 
				), 
				'name' => 'import_description', 
				'selected' => $options[ 'import_description' ] 
		);
		cvm_select( $args );
		?><br />

<label for="import_status"><?php _e('Import status', 'codeflavors-vimeo-video-post-lite')?>:</label>
<?php
		$args = array( 
				'options' => array( 
						'publish' => __( 'Published', 'codeflavors-vimeo-video-post-lite' ), 
						'draft' => __( 'Draft', 'codeflavors-vimeo-video-post-lite' ), 
						'pending' => __( 'Pending', 'codeflavors-vimeo-video-post-lite' ) 
				), 
				'name' => 'import_status', 
				'selected' => $options[ 'import_status' ] 
		);
		cvm_select( $args );
		?><br />

<label for="import_title"><?php _e('Import titles', 'codeflavors-vimeo-video-post-lite')?>:</label>
<input type="checkbox" value="1" id="import_title" name="import_title"
	<?php cvm_check($options['import_title']);?> />

<?php
		$theme_support = cvm_check_theme_support();
		if( $theme_support ):
			?>
<br />
<label for="cvm_theme_import"><?php printf( __('Import in <strong>%s</strong><sup>PRO</sup>?', 'codeflavors-vimeo-video-post-lite'), $theme_support['theme_name']);?></label>
<input type="checkbox" disabled="disabled" />

		<?php 
			endif
		?>

<div id="cvm-import-videos-submit-c">
		<?php submit_button( __('Import videos', 'codeflavors-vimeo-video-post-lite'), 'primary', 'cvm-import-button', false );?>
		<span class="cvm-ajax-response"></span>
</div>
<input type="hidden" name="action_top" id="action_top" value="import" />
<?php
	}

	/**
	 * Import categories meta box
	 */
	public function import_categories_meta(){
		echo '<ul id="cvm-list-categories-checkboxes">';
		wp_terms_checklist( 0, array( 
				'taxonomy' => $this->taxonomy 
		) );
		echo '</ul>';
		?>

<a id="cvm-toggle-new-cat" href="#"><?php _e('Add new', 'codeflavors-vimeo-video-post-lite');?></a>
<div style="display: none;" id="cvm-add-video-category">			
			<?php wp_nonce_field('cvm-create-category', 'wp_nonce');?>
			<input type="text" name="cvm-ajax-cat-new" value="" /> <input
		type="hidden" name="action" value="cvm-create-video-category" /> <input
		type="button" id="cvm-new-category" name="cvm-new-category"
		class="secondary button"
		value="<?php _e('Create category', 'codeflavors-vimeo-video-post-lite');?>" />
</div>
<?php
	}

	/**
	 * Create category ajax
	 */
	public function ajax_new_category(){
		if( ! check_admin_referer( 'cvm-create-category', 'wp_nonce' ) ){
			wp_die( "Cheatin' uh?" );
		}
		
		$response = array( 
				'error' => false, 
				'output' => false, 
				'existing' => false 
		);
		
		if( ! isset( $_POST[ 'cvm-ajax-cat-new' ] ) ){
			$response[ 'error' ] = __( "Category couldn't be identified. Please try again.", 'codeflavors-vimeo-video-post-lite' );
		}
		if( $response[ 'error' ] && empty( $_POST[ 'cvm-ajax-cat-new' ] ) ){
			$response[ 'error' ] = __( 'Please enter a category name.', 'codeflavors-vimeo-video-post-lite' );
		}
		
		if( ! $response[ 'error' ] ){
			// check if category exists
			$term = term_exists( $_POST[ 'cvm-ajax-cat-new' ], $this->taxonomy );
			
			if( 0 == $term || null == $term ){
				// create the category
				$term = wp_insert_term( $_POST[ 'cvm-ajax-cat-new' ], $this->taxonomy );
				$tax_term = get_term( $term[ 'term_id' ], $this->taxonomy );
				$response[ 'output' ] = sprintf( '<label><input type="checkbox" checked="checked" name="tax_input[vimeo-videos][]" value="%d"> %s</label>', $tax_term->term_id, $tax_term->name );
			}else{
				$response[ 'error' ] = __( 'Category already exists.', 'codeflavors-vimeo-video-post-lite' );
				$response[ 'existing' ] = $term[ 'term_id' ];
			}
		}
		
		echo json_encode( $response );
		die();
	}

	/**
	 * Import thumbnail as featured image in single post page
	 */
	public function import_thumbnail() {
		if ( ! current_user_can( 'upload_files' ) ) {
			header( 'HTTP/1.1 401 Unauthorized' );
			_e( 'You do not have the neccessary permissions.', 'cvm_vimeo' );
			die();
		}

		if ( ! isset( $_POST['post'] ) ) {
			die();
		}

		$post_id   = absint( $_POST['post'] );
		$refresh   = (bool) $_POST['refresh'];
		$thumbnail = cvm_import_featured_image( $post_id, $refresh );

		if ( ! $thumbnail ) {
			wp_send_json_error( __( 'Image could not be retrieved', 'codeflavors-vimeo-video-post-lite' ) );
		}

		// If request is sent by Gutenberg script, it will contain a POST variable called "gutenberg"
		if( isset( $_POST['gutenberg'] ) ){
			$response = $thumbnail;
		}else{
			$response = _wp_post_thumbnail_html( $thumbnail['attachment_id'], $thumbnail['post_id'] );
		}

		wp_send_json_success( $response );
	}

	/**
	 * On video import page load, perform actions
	 */
	public function video_import_onload(){
		$this->video_import_assets();
		
		// search videos result
		if( isset( $_GET[ 'cvm_search_nonce' ] ) ){
			if( check_admin_referer( 'cvm-video-import', 'cvm_search_nonce' ) ){
				
				require_once CVM_PATH . '/includes/libs/video-import-list-table.class.php';
				global $CVM_List_Table;
				
				$screen = get_current_screen();
				$CVM_List_Table = new CVM_Video_Import_List_Table( array( 
						'screen' => $screen->id 
				) );
			}
		}
		
		// import videos / alternative to AJAX import
		if( isset( $_REQUEST[ 'cvm_import_nonce' ] ) ){
			if( check_admin_referer( 'cvm-import-videos-to-wp', 'cvm_import_nonce' ) ){
				if( 'import' == $_REQUEST[ 'action_top' ] || 'import' == $_REQUEST[ 'action2' ] ){
					$this->import_videos();
				}
				$options = cvm_get_settings();
				wp_redirect( 'edit.php?post_status=' . $options[ 'import_status' ] . '&post_type=' . $this->post_type );
				exit();
			}
		}
	}

	/**
	 * Import videos to WordPress.
	 * Used on manual bulk import
	 */
	private function import_videos(){
		if( ! isset( $_POST[ 'cvm_import' ] ) ){
			return false;
		}
		
		$videos = ( array ) $_POST[ 'cvm_import' ];
		
		$result = array( 
				'imported' => 0, 
				'skipped' => 0, 
				'total' => count( $videos ) 
		);
		
		// get import options
		$import_options = cvm_get_settings();
		if( ! isset( $_POST[ 'import_title' ] ) ){
			$import_options[ 'import_title' ] = false;
		}
		if( isset( $_POST[ 'import_status' ] ) && in_array( $_POST[ 'import_status' ], array( 
				'publish', 
				'draft', 
				'pending' 
		) ) ){
			$import_options[ 'import_status' ] = $_POST[ 'import_status' ];
		}
		if( isset( $_POST[ 'import_description' ] ) && in_array( $_POST[ 'import_description' ], array( 
				'content', 
				'excerpt', 
				'content_excerpt', 
				'none' 
		) ) ){
			$import_options[ 'import_description' ] = $_POST[ 'import_description' ];
		}
		
		$statuses = array( 
				'publish', 
				'draft', 
				'pending' 
		);
		$status = in_array( $import_options[ 'import_status' ], $statuses ) ? $import_options[ 'import_status' ] : 'draft';
		
		$category = false;
		if( isset( $_REQUEST[ 'tax_input' ][ $this->taxonomy ] ) ){
			$category = $_REQUEST[ 'tax_input' ][ $this->taxonomy ];
		}
		
		// theme import
		$theme_import = false;
		$post_type = $this->post_type;
		
		/**
		 * Filter that allows changing of post format when importing videos
		 * @var string - post format
		 */
		$post_format = apply_filters( 'cvm_import_post_format' , 'video' );
		
		foreach( $videos as $video_id ){
			
			// search if video already exists
			$posts = get_posts( array( 
					'post_type' => $post_type, 
					'meta_key' => '__cvm_video_id', 
					'meta_value' => $video_id, 
					'post_status' => array( 
							'publish', 
							'pending', 
							'draft', 
							'future', 
							'private' 
					) 
			) );
			// video already exists, don't do anything
			if( $posts ){
				$result[ 'skipped' ] += 1;
				continue;
			}
			
			// get video details
			$video = cvm_query_video( $video_id );
			if( $video && !is_wp_error( $video ) ){
			    $post_content = '';
				if( 'content' == $import_options[ 'import_description' ] || 'content_excerpt' == $import_options[ 'import_description' ] ){
					$post_content = $video[ 'description' ];
				}
				$post_excerpt = '';
				if( 'excerpt' == $import_options[ 'import_description' ] || 'content_excerpt' == $import_options[ 'import_description' ] ){
					$post_excerpt = $video[ 'description' ];
				}
				
				$post_title = $import_options[ 'import_title' ] ? $video[ 'title' ] : '';
				
				// insert the post
				$post_data = array(
						/**
						 * Filter on post title
						 *
						 * @param string - the post title
						 * @param array - the video details
						 * @param bool/array - always false, implemented for PRO version reasons
						 */
						'post_title' => apply_filters('cvm_video_post_title', $post_title, $video, false), 
						/**
						 * Filter on post content
						 * 
						 * @param string - the post content
						 * @param array - the video details
						 * @param bool/array - always false, implemented for PRO version reasons
						 */
						'post_content' 	=> apply_filters('cvm_video_post_content', $post_content, $video, false),
						/**
						 * Filter on post excerpt
						 * 
						 * @param string - the post excerpt
						 * @param array - the video details
						 * @param bool/array - always false, implemented for PRO version reasons
						 */
						'post_excerpt'	=> apply_filters('cvm_video_post_excerpt', $post_excerpt, $video, false),
						'post_type' => $post_type, 
						/**
						 * Filter on post status
						 *
						 * @param string - the post status
						 * @param array - the video details
						 * @param bool/array - always false, implemented for PRO version reasons
						 */
						'post_status'	=> apply_filters('cvm_video_post_status', $status, $video, false ) 
				);

				$pd = $import_options['import_date'] ? date( 'Y-m-d H:i:s', strtotime( $video['published'] ) ) : current_time( 'mysql' );
				/**
				 * Filter on post date
				 *
				 * @param string - the post date
				 * @param array - the video details
				 * @param bool/array - false if not imported as theme, array if imported as theme and theme is active
				 */
				$post_date = apply_filters( 'cvm_video_post_date', $pd, $video, false );

				if ( isset( $import_options['import_date'] ) && $import_options['import_date'] ) {
					$post_data['post_date_gmt'] = $post_date;
					$post_data['edit_date']     = $post_date;
					$post_data['post_date']     = $post_date;
				}

				// In Vimeo, post content can be empty. In this case, WP will issue an error because it doesn't allow empty content.
				// Force some content into post_content if empty
				if ( empty( $post_data['post_content'] ) ) {
					$post_data['post_content'] = '&nbsp;';
				}

				$post_id = wp_insert_post( $post_data, true );
				
				// check if post was created
				if( ! is_wp_error( $post_id ) ){
					// set post format
					if( $post_format  ){
						set_post_format( $post_id, $post_format );
					}
					
					$result[ 'imported' ] += 1;
					
					if( $category ){
						if( ! is_array( $category ) ){
							( array ) $category;
						}
						wp_set_post_terms( $post_id, $category, $this->taxonomy );
					}

					// insert tags
					if ( ( isset( $import_options['import_tags'] ) && $import_options['import_tags'] ) && $this->get_tag_tax() ) {
						if ( is_array( $video['tags'] ) ) {
							//$tags  = array();
							$tags  = array_slice( $video['tags'], 0, 1 );
							if ( $tags ) {
								wp_set_post_terms( $post_id, $tags, $this->get_tag_tax(), true );
							}
						}
					}

					/**
					 * Action on post insert that allows setting of different meta on post
                     *
                     * @param int $post_id - the ID of the newly created post
                     * @param array $video - video details array
                     * @param boolean $theme_import - always false for Lite version
                     * @param string $post_type - the post type created
					 */
                    do_action('cvm_post_insert', $post_id, $video, $theme_import, $post_type);

					/**
					 * For plugin post type, save the meta
					 */
					// set some meta on video post
					unset( $video[ 'title' ] );
					unset( $video[ 'description' ] );
					update_post_meta( $post_id, '__cvm_video_id', $video[ 'video_id' ] );
					update_post_meta( $post_id, '__cvm_video_data', $video );
					update_post_meta( $post_id, '__cvm_source', $_POST[ 'cvm_source' ] );
				}
			}
		}
		
		return $result;
	}

	/**
	 * When trying to insert an empty post, WP is running a filter.
	 * Given the fact that
	 * users are allowed to insert empty posts when importing, the filter will return
	 * false on maybe_empty to allow insertion of video.
	 * Filter is activated inside function import_videos()
	 * 
	 * @param bool $maybe_empty
	 * @param array $postarr
	 */
	public function force_empty_insert( $maybe_empty, $postarr ){
		if( $this->post_type == $postarr[ 'post_type' ] ){
			return false;
		}
	}

	/**
	 * Ajax response to video import action
	 */
	public function ajax_import_videos(){
		// import videos
		$response = array( 
				'success' => false, 
				'error' => false 
		);
		
		if( isset( $_POST[ 'cvm_import_nonce' ] ) ){
			if( check_admin_referer( 'cvm-import-videos-to-wp', 'cvm_import_nonce' ) ){
				if( 'import' == $_REQUEST[ 'action_top' ] || 'import' == $_REQUEST[ 'action2' ] ){
					$result = $this->import_videos();
					
					if( $result ){
						$response[ 'success' ] = sprintf( __( 'Out of %d videos, %d were successfully imported and %d were skipped.', 'codeflavors-vimeo-video-post-lite' ), $result[ 'total' ], $result[ 'imported' ], $result[ 'skipped' ] );
					}else{
						$response[ 'error' ] = __( 'No videos selected for importing. Please select some videos by checking the checkboxes next to video title.', 'codeflavors-vimeo-video-post-lite' );
					}
				}else{
					$response[ 'error' ] = __( 'Please select an action.', 'codeflavors-vimeo-video-post-lite' );
				}
			}else{
				$response[ 'error' ] = __( "Cheatin' uh?", 'codeflavors-vimeo-video-post-lite' );
			}
		}else{
			$response[ 'error' ] = __( "Cheatin' uh?", 'codeflavors-vimeo-video-post-lite' );
		}
		
		echo json_encode( $response );
		die();
	}

	/**
	 * Enqueue scripts and styles needed on import page
	 */
	private function video_import_assets(){
		// video import form functionality
		wp_enqueue_script( 'cvm-video-search-js', CVM_URL . 'assets/back-end/js/video-import.js', array( 
				'jquery' 
		), '1.0' );
		wp_localize_script( 'cvm-video-search-js', 'cvm_importMessages', array( 
				'loading' => __( 'Importing, please wait...', 'codeflavors-vimeo-video-post-lite' ), 
				'wait' => __( "Not done yet, still importing. You'll have to wait a bit longer.", 'codeflavors-vimeo-video-post-lite' ) 
		) );
		
		wp_enqueue_style( 'cvm-video-search-css', CVM_URL . 'assets/back-end/css/video-import.css', array(), '1.0' );
	}

	/**
	 * Output plugin settings page
	 */
	public function plugin_settings(){
		$options = cvm_get_settings();
		$player_opt = cvm_get_player_settings();
		
		// fire up Vimeo class
		require_once CVM_PATH . 'includes/libs/vimeo.class.php';
		$vimeo = new CVM_Vimeo();
		
		if( ! empty( $options[ 'vimeo_consumer_key' ] ) && ! empty( $options[ 'vimeo_secret_key' ] ) ){
			if( empty( $options[ 'oauth_token' ] ) ){
				// account token
				$token = $vimeo->get_unauth_token();
				if( ! is_wp_error( $token ) ){
					$options[ 'oauth_token' ] = $token;
				}else{
					$authorize_error = $token->get_error_message();
					$options['vimeo_consumer_key'] = '';
					$options['vimeo_secret_key'] = '';
				}
				update_option( '_cvm_plugin_settings', $options );
			}
		}

		$show_auth_message = false;
		if ( ! empty( $options['vimeo_consumer_key'] ) && ! empty( $options['vimeo_secret_key'] ) && $options['oauth_token'] ) {
			$show_auth_message = true;
		}

		include CVM_PATH . 'views/plugin_settings.php';
	}

	/**
	 * Process plugin settings
	 */
	public function plugin_settings_onload(){
		if( isset( $_POST[ 'cvm_wp_nonce' ] ) ){
			if( check_admin_referer( 'cvm-save-plugin-settings', 'cvm_wp_nonce' ) ){
				cvm_update_settings();
				cvm_update_player_settings();
			}
			wp_redirect( 'edit.php?post_type=' . $this->post_type . '&page=cvm_settings', false );
			die();
		}

		if ( isset( $_GET['clear_oauth'] ) && 'true' == $_GET['clear_oauth'] ) {
			if ( check_admin_referer( 'cvm-clear-oauth-token', 'cvm_nonce' ) ) {
				$_POST['vimeo_consumer_key']   = '';
				$_POST['vimeo_secret_key']     = '';
				$_POST['oauth_token']          = '';
				cvm_update_settings();
			}
			wp_redirect( 'edit.php?post_type=' . $this->post_type . '&page=cvm_settings', false );
			die();
		}

		wp_enqueue_style( 'cvm-plugin-settings', CVM_URL . 'assets/back-end/css/plugin-settings.css', false );
		
		wp_enqueue_script( 'cvm-tabs', CVM_URL . 'assets/back-end/js/tabs.js', array( 
				'jquery', 
				'jquery-ui-tabs' 
		) );
		
		wp_enqueue_script( 'cvm-video-edit', CVM_URL . 'assets/back-end/js/video-edit.js', array( 
				'jquery' 
		), '1.0' );

		wp_enqueue_script( 'cvm-plugin-settings', CVM_URL . 'assets/back-end/js/plugin-settings.js', array(
			'jquery'
		), '1.0' );
	}

	/**
	 * Outputs a link that allows users to clear OAuth credentials
	 *
	 * @param string $text
	 * @param string $echo
	 *
	 * @return void|string
	 */
	private function clear_oauth_credentials_link( $text = '', $class = '', $echo = true ) {
		if ( empty( $text ) ) {
			$text = __( 'Clear OAuth credentials', 'codeflavors-vimeo-video-post-lite' );
		}

		$options = cvm_get_settings();
		if ( empty( $options['vimeo_consumer_key'] ) || empty( $options['vimeo_secret_key'] ) ) {
			return;
		}

		$nonce  = wp_create_nonce( 'cvm-clear-oauth-token' );
		$url    = menu_page_url( 'cvm_settings', false ) . '&clear_oauth=true&cvm_nonce=' . $nonce . '#cvm-settings-auth-options';
		$output = sprintf( '<a href="%s" class="%s">%s</a>', $url, esc_attr( $class ), $text );

		if ( $echo ) {
			echo $output;
		}

		return $output;
	}

	public function go_pro_page(){
        include_once CVM_PATH . 'views/go_pro.php';
    }

    public function go_pro_page_onload(){
        remove_all_actions( 'admin_notices' );
        wp_enqueue_style( 'cvm_gopro', CVM_URL . 'assets/back-end/css/gopro.css' );
        cvm_enqueue_player();
    }


	/**
	 * Manipulate output for featured image on custom post to allow importing of thumbnail as featured image
	 */
	public function post_thumbnail_meta_panel( $content, $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return $content;
		}

		$video_data = $this->get_video_data( $post->ID );
		if ( ! $video_data ) {
			return $content;
		}

		$has_thumbnail = get_post_meta( $post_id, '_thumbnail_id', true );
		if ( !isset( $video_data['thumbnails'] ) || !$video_data['thumbnails'] ) {
			$has_thumbnail = true;
		}

		if ( $has_thumbnail ) {
			$content .= '<hr /><br />';
			$content .= sprintf( '<a href="#" id="cvm-import-video-thumbnail" data-refresh="1" data-post="%d"><i class="dashicons dashicons-update"></i> %s</a>', $post_id, __( 'Refresh Vimeo image', 'codeflavors-vimeo-video-post-lite' ) );
			$content .= '<br /><p class="description" id="cvm-thumb-response">' . __( 'Will import a fresh image from Vimeo. If already existing, it will be duplicated.', 'codeflavors-vimeo-video-post-lite' ) . '</p>';
		} else {
			$content .= '<hr /><br />';
			$content .= sprintf( '<a href="#" id="cvm-import-video-thumbnail" data-refresh="0" data-post="%d"><i class="dashicons dashicons-download"></i >%s</a>', $post_id, __( 'Import Vimeo image', 'codeflavors-vimeo-video-post-lite' ) );
			$content .= '<br><p class="description" id="cvm-thumb-response">' . __( 'Will first search the Media Gallery for an already imported image and will import if none found.', 'codeflavors-vimeo-video-post-lite' ) . '</p>';
		}

		return $content;
	}

	/**
	 * Privacy policy plugin information
	 */
	public function privacy_policy(){
		if( !function_exists( 'wp_add_privacy_policy_content' ) ){
			return;
		}

		$policy_content = sprintf(
			__( 'By using the embed feature of this plugin you will be agreeing to Vimeo\'s privacy policy. More details can be found here: %s', 'codeflavors-vimeo-video-post-lite' ),
			'https://vimeo.com/privacy'
		);

		wp_add_privacy_policy_content( 'CodeFlavors Vimeotheque Lite', $policy_content );
	}

	/**
	 * Add meta boxes on video post type
	 */
	public function add_meta_boxes(){
		add_meta_box( 'cvm-video-settings', __( 'Video settings', 'codeflavors-vimeo-video-post-lite' ), array( 
				$this, 
				'post_video_settings_meta_box' 
		), $this->post_type, 'normal', 'high' );
		
		add_meta_box( 'cvm-show-video', __( 'Live video', 'codeflavors-vimeo-video-post-lite' ), array( 
				$this, 
				'post_show_video_meta_box' 
		), $this->post_type, 'normal', 'high' );
		
		// add meta box to insert videos to post page
		$post_types = get_post_types( array( 
				'public' => true, 
				'_builtin' => false 
		), 'names' );
		if( ! is_array( $post_types ) ){
			$post_types = array();
		}
		$post_types[ 'post' ] = 'post';
		$post_types[ 'page' ] = 'page';
		
		foreach( $post_types as $type ){
			if( $this->post_type == $type ){
				continue;
			}
			
			add_meta_box( 'cvm-add-video', __( 'Video shortcode', 'codeflavors-vimeo-video-post-lite' ), array( 
					$this, 
					'post_shortcode_meta_box' 
			), $type, 'side' );
		}
	}

	/**
	 * Post add shortcode meta box output
	 */
	public function post_shortcode_meta_box(){
		?>
<p><?php _e('Add video/playlist into post.', 'codeflavors-vimeo-video-post-lite');?>
<p>
	<a class="button" href="#" id="cvm-shortcode-2-post"
		title="<?php _e('Add shortcode');?>"><?php _e('Add video shortcode');?></a>
		<?php
	}

	/**
	 * Save post data from meta boxes.
	 * Hooked to save_post
	 */
	public function save_post( $post_id, $post ){
		if( ! isset( $_POST[ 'cvm-video-nonce' ] ) ){
			return;
		}
		// check if post is the correct type
		if( $this->post_type != $post->post_type ){
			return;
		}
		// check if user can edit
		if( ! current_user_can( 'edit_post', $post_id ) ){
			return;
		}
		// check nonce
		if( ! check_admin_referer( 'cvm-save-video-settings', 'cvm-video-nonce' ) ){
			return;
		}
		cvm_update_video_settings( $post_id );
	}

	/**
	 * Display live video meta box
	 */
	public function post_show_video_meta_box(){
		global $post;
		$video_id = get_post_meta( $post->ID, '__cvm_video_id', true );
		$video_data = get_post_meta( $post->ID, '__cvm_video_data', true );
		?>	
<script language="javascript">
;(function($){
	$(document).ready(function(){
		$('#cvm-video-preview').Vimeo_VideoPlayer({
			'video_id' 	: '<?php echo $video_data['video_id'];?>',
			'source'	: 'vimeo'
		});
	})
})(jQuery);
</script>
<div id="cvm-video-preview"
	style="height: 315px; width: 560px; max-width: 100%;"></div>
<?php
	}

	public function post_video_settings_meta_box( $post ){
		$settings = cvm_get_video_settings( $post->ID );
		?>
<?php wp_nonce_field('cvm-save-video-settings', 'cvm-video-nonce');?>
<table class="form-table cvm-player-settings-options">
	<tbody>
		<tr>
			<th><label for="cvm_aspect_ratio"><?php _e('Player size', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td><label for="cvm_aspect_ratio"><?php _e('Aspect ratio');?>:</label>
				<?php
				$args = array(
					'name'     => 'aspect_ratio',
					'id'       => 'cvm_aspect_ratio',
					'class'    => 'cvm_aspect_ratio',
					'selected' => $settings[ 'aspect_ratio' ]
				);
				cvm_aspect_ratio_select( $args );
		        ?>
				<label for="cvm_width"><?php _e('Width', 'codeflavors-vimeo-video-post-lite');?> :</label>
				<input type="number" name="width" id="cvm_width" class="cvm_width"
				value="<?php echo $settings['width'];?>" size="2" style="width: 5em;" placeholder="<?php esc_attr_e( 'Enter the optimal width for your video.', 'codeflavors-vimeo-video-post-lite' ); ?>" />px
				| <?php _e('Height', 'codeflavors-vimeo-video-post-lite');?> : <span
				class="cvm_height" id="cvm_calc_height"><?php echo cvm_player_height( $settings['aspect_ratio'], $settings['width'] );?></span>px
			</td>
		</tr>

		<tr>
			<th><label for="cvm_video_position"><?php _e('Video position','codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td>
				<?php
		$args = array( 
				'options' => array( 
						'above-content' => __( 'Above post content', 'codeflavors-vimeo-video-post-lite' ), 
						'below-content' => __( 'Below post content', 'codeflavors-vimeo-video-post-lite' ) 
				), 
				'name' => 'video_position', 
				'id' => 'cvm_video_position', 
				'selected' => $settings[ 'video_position' ] 
		);
		cvm_select( $args );
		?>
			</td>
		</tr>
		<tr>
			<th><label for="cvm_volume"><?php _e('Volume', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td><input type="number" name="volume" id="cvm_volume"
                       value="<?php echo $settings['volume'];?>" size="1" maxlength="3" min="0" max="100" style="width: 4em;" placeholder="<?php esc_attr_e( 'Video volume', 'codeflavors-vimeo-video-post-lite' );?>" />
                <label for="cvm_volume"><span class="description">( <?php _e('number between 0 (mute) and 100 (max)', 'codeflavors-vimeo-video-post-lite');?> )</span></label>
			</td>
		</tr>
		<tr>
			<th><label for="cvm_autoplay"><?php _e('Autoplay', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td><input name="autoplay" id="cvm_autoplay" type="checkbox"
				value="1" <?php cvm_check((bool)$settings['autoplay']);?> /> <label
				for="cvm_autoplay"><span class="description">( <?php _e('when checked, video will start playing once page is loaded', 'codeflavors-vimeo-video-post-lite');?> )</span></label>
			</td>
		</tr>

		<tr>
			<th><label for="title"><?php _e('Show video title', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td><input name="title" id="cvm_title" class="cvm_title"
				type="checkbox" value="1"
				<?php cvm_check((bool)$settings['title']);?> /> <label
				for="cvm_title"><span class="description">( <?php _e('when checked, player will display video title', 'codeflavors-vimeo-video-post-lite');?> )</span></label>
			</td>
		</tr>

		<tr>
			<th><label for="cvm_fullscreen"><?php _e('Allow full screen', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td><input name="fullscreen" id="cvm_fullscreen" type="checkbox"
				value="1" <?php cvm_check((bool)$settings['fullscreen']);?> /></td>
		</tr>

		<tr>
			<th><label for="cvm_color"><?php _e('Player color', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
			<td>#<input type="text" name="color" id="cvm_color"
				value="<?php echo $settings['color'];?>" /></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="byline"><?php _e('Show video author', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
			<td><input type="checkbox" value="1" id="byline" name="byline"
				<?php cvm_check( (bool)$settings['byline'] );?> /> <span
				class="description"><?php _e('When checked, player will display video uploader.', 'codeflavors-vimeo-video-post-lite');?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="portrait"><?php _e('Author portrait', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
			<td><input type="checkbox" value="1" id="portrait" name="portrait"
				<?php cvm_check( (bool)$settings['portrait'] );?> /> <span
				class="description"><?php _e('When checked, player will display uploader image.', 'codeflavors-vimeo-video-post-lite');?></span>
			</td>
		</tr>

	</tbody>
</table>
<?php
	}

	/**
	 * Add scripts to custom post edit page
	 * 
	 * @param unknown_type $hook
	 */
	public function post_edit_assets( $hook ){
		if( 'post.php' !== $hook ){
			return;
		}
		global $post;
		if( $this->post_type !== $post->post_type ){
			return;
		}
		
		// add video player for video preview on post
		cvm_enqueue_player();
		wp_enqueue_script( 'cvm-video-edit', CVM_URL . 'assets/back-end/js/video-edit.js', array( 
				'jquery' 
		), '1.0' );
	}

	/**
	 * New post load action for videos.
	 * Will first display a form to query for the video.
	 */
	public function post_new_onload(){
		if( ! isset( $_REQUEST[ 'post_type' ] ) || $this->post_type !== $_REQUEST[ 'post_type' ] ){
			return;
		}
		
		global $CVM_NEW_VIDEO;
		
		if( isset( $_POST[ 'wp_nonce' ] ) ){
			if( check_admin_referer( 'cvm_query_new_video', 'wp_nonce' ) ){
				
				if( empty( $_POST[ 'cvm_video_id' ] ) ){
					wp_redirect( 'post-new.php?post_type=' . $this->post_type );
					die();
				}
				
				// search if video already exists
				$posts = get_posts( array( 
						'post_type' => $this->post_type, 
						'meta_key' => '__cvm_video_id', 
						'meta_value' => $_POST[ 'cvm_video_id' ], 
						'post_status' => array( 
								'publish', 
								'pending', 
								'draft', 
								'future', 
								'private' 
						) 
				) );
				if( $posts ){
					wp_redirect( 'post.php?post=' . $posts[ 0 ]->ID . '&action=edit&message=102&video_id=' . $_POST[ 'cvm_video_id' ] );
					die();
				}
				
				$video = cvm_query_video( $_POST[ 'cvm_video_id' ] );
				if( $video && !is_wp_error( $video ) ){
					$CVM_NEW_VIDEO = $video;
					
					add_filter( 'default_content', array( 
							$this, 
							'default_content' 
					), 999, 2 );
					add_filter( 'default_title', array( 
							$this, 
							'default_title' 
					), 999, 2 );
					add_filter( 'default_excerpt', array( 
							$this, 
							'default_excerpt' 
					), 999, 2 );
					
					// add video player for video preview on post
					cvm_enqueue_player();
				}
			}else{
				wp_die( "Cheatin' uh?" );
			}
		}
		// if vidoe query not started, display the form
		if( ! $CVM_NEW_VIDEO ){
			wp_enqueue_script( 'cvm-new-video-js', CVM_URL . 'assets/back-end/js/video-new.js', array( 
					'jquery' 
			), '1.0' );
			
			$post_type_object = get_post_type_object( $this->post_type );
			$title = $post_type_object->labels->add_new_item;
			
			include ABSPATH . 'wp-admin/admin-header.php';
			include CVM_PATH . 'views/new_video.php';
			include ABSPATH . 'wp-admin/admin-footer.php';
			die();
		}
	}

	/**
	 * Set video description on new post
	 * 
	 * @param string $post_content
	 * @param object $post
	 */
	public function default_content( $post_content, $post ){
		global $CVM_NEW_VIDEO;
		if( ! $CVM_NEW_VIDEO ){
			return;
		}
		
		return $CVM_NEW_VIDEO[ 'description' ];
	}

	/**
	 * Set video title on new post
	 * 
	 * @param string $post_title
	 * @param object $post
	 */
	public function default_title( $post_title, $post ){
		global $CVM_NEW_VIDEO;
		if( ! $CVM_NEW_VIDEO ){
			return;
		}
		
		return $CVM_NEW_VIDEO[ 'title' ];
	}

	/**
	 * Set video excerpt on new post, add taxonomies and save meta
	 * 
	 * @param string $post_excerpt
	 * @param object $post
	 */
	public function default_excerpt( $post_excerpt, $post ){
		global $CVM_NEW_VIDEO;
		if( ! $CVM_NEW_VIDEO ){
			return;
		}

		// import video thumbnail as featured image
		$import_options = cvm_get_settings();

		$pd = $import_options['import_date'] ? date( 'Y-m-d H:i:s', strtotime( $CVM_NEW_VIDEO['published'] ) ) : current_time( 'mysql' );
		/**
		 * Filter on post date
		 *
		 * @param string - the post date
		 * @param array - the video details
		 * @param bool/array - false if not imported as theme, array if imported as theme and theme is active
		 */
		$post_date = apply_filters( 'cvm_video_post_date', $pd, $CVM_NEW_VIDEO, false );

		// set post date
		if ( $import_options['import_date'] ) {
			$postarr = array(
				'ID'            => $post->ID,
				'post_date_gmt' => $post_date,
				'edit_date'     => $post_date,
				'post_date'     => $post_date,
				// To avoid overriding the tags already set, set tags_input to NULL (WP 4.8.2 wp-includes/post.php:3339)
				'tags_input'   => null
			);
			wp_update_post( $postarr );
		}

		// insert tags
		if ( ( isset( $import_options['import_tags'] ) && $import_options['import_tags'] ) && $this->get_tag_tax() ) {
			if ( is_array( $CVM_NEW_VIDEO['tags'] ) ) {
				//$tags  = array();
				$tags  = array_slice( $CVM_NEW_VIDEO['tags'], 0, 1 );
				if ( $tags ) {
					wp_set_post_terms( $post->ID, $tags, $this->get_tag_tax(), true );
				}
			}
		}

		// set video ID on post meta
		update_post_meta( $post->ID, '__cvm_video_id', $CVM_NEW_VIDEO[ 'video_id' ] );
		
		// process video as plugin custom post type
		// set some meta on video post
		unset( $CVM_NEW_VIDEO[ 'title' ] );
		unset( $CVM_NEW_VIDEO[ 'description' ] );
		update_post_meta( $post->ID, '__cvm_video_data', $CVM_NEW_VIDEO );
		update_post_meta( $post->ID, '__cvm_source', $_POST[ 'cvm_source' ] );
	}

	/**
	 * Triggered on plugin activation
	 */
	public function plugin_activation() {
		set_transient( 'cvm_plugin_activation', true, 30 );
	}

	/**
	 * Admin init callback, redirects to plugin Settings page after plugin activation.
	 */
	public function activation_redirect() {
		$t = get_transient( 'cvm_plugin_activation' );
		if ( $t ) {
			delete_transient( 'cvm_plugin_activation' );
			wp_redirect( str_replace( '#038;', '&', menu_page_url( 'cvm_about', false ) ) );
			die();
		}
	}

	/**
	 * Callback on Gutenberg's script enqueue action
	 * Enqueues all necessary files for Gutenberg compatibility
	 */
	public function gutenberg_editor(){
		wp_enqueue_script( 'cvm-gutenberg', CVM_URL . 'assets/back-end/js/gutenberg/video-thumbnail.js', array(
			'jquery'
		), '1.0' );
		wp_dequeue_script( 'cvm-video-thumbnail' );
		$this->is_gutenberg = true;
	}

	/**
     * Returns whether Gutenberg editor is loaded and in use
	 * @return bool
	 */
	public function is_gutenberg(){
	    return $this->is_gutenberg;
    }

    public function about_page(){

	    include_once CVM_PATH . 'views/about.php';
    }

    public function about_page_onload(){

    }

	/**
	 * Returns post video data for a given post ID
	 * 
	 * @param unknown $post_id
	 */
	public function get_video_data( $post_id ){
		if( ! $post_id ){
			return false;
		}
		if( ! is_numeric( $post_id ) ){
			if( ! is_object( $post_id ) ){
				return false;
			}
			
			$post_id = $post_id->ID;
		}
		
		$meta = get_post_meta( $post_id, '__cvm_video_data', true );
		if( ! $meta ){
			return;
		}
		
		if( stristr( $meta[ 'video_id' ], ':' ) ){
			$parts = explode( ':', $meta[ 'video_id' ] );
			$meta[ 'video_id' ] = $parts[ 0 ];
		}
		
		return $meta;
	}

	/**
	 * Return post type
	 */
	public function get_post_type(){
		return $this->post_type;
	}

	/**
	 * Return taxonomy
	 */
	public function get_post_tax(){
		return $this->taxonomy;
	}

	/**
	 * Returns tags taxonomy
	 */
	public function get_tag_tax(){
		return $this->tag;
	}

	/**
	 * Return playlist post type
	 */
	public function get_playlist_post_type(){
		return $this->playlist_type;
	}

	/**
	 * Return playlist meta key name
	 */
	public function get_playlist_meta_name(){
		return $this->playlist_meta;
	}
}

global $CVM_POST_TYPE;
$CVM_POST_TYPE = new CVM_Video_Post_Type();
