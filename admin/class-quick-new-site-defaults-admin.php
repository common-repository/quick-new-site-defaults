<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://stehle-internet.de/
 * @since      1.0.0
 *
 * @package    Quick_New_Site_Defaults
 * @subpackage Quick_New_Site_Defaults/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Quick_New_Site_Defaults
 * @subpackage Quick_New_Site_Defaults/admin
 * @author     Martin Stehle <shop@stehle-internet.de>
 */
class Quick_New_Site_Defaults_Admin {

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The name of this plugin.
	 */
	private $plugin_name;

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Multiple used translated string
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $failed_change_label    The translated string about failed option change.
	 */
	private $failed_change_label;

	/**
	 * Multiple used translated string
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $successful_change_label    The translated string about successful option change.
	 */
	private $successful_change_label;

	/**
	 * ID of current user
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      integer    $user_id    The ID of the current user
	 */
	private $user_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $plugin_slug       The slug of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_slug, $version ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_slug = $plugin_slug;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		
		$label = 'Settings';
		$page_title = sprintf( '%s: %s', $this->plugin_name, __( $label ) );

		// Add a settings page for this plugin to the Settings menu.
		$this->plugin_screen_id = add_options_page(
			$page_title,
			$this->plugin_name,
			'manage_options',
			$this->plugin_slug,
			array( $this, 'main' )
		);

	}

	/**
	 * Print a message about the location of the plugin in the WP backend
	 * 
	 * @since    1.0.0
	 */
	public function display_activation_message () {

		$text = 'Settings';
		
		if ( is_rtl() ) {
			$sep = '&lsaquo;';
			// set link #2
			$link = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( sprintf( 'options-general.php?page=%s', $this->plugin_slug ) ) ),
				$this->plugin_name,
				$sep,
				esc_html__( $text )
			);
		} else {
			$sep = '&rsaquo;';
			// set link #2
			$link = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( sprintf( 'options-general.php?page=%s', $this->plugin_slug ) ) ),
				esc_html__( $text ),
				$sep,
				$this->plugin_name
			);
		}
		
		// set whole message
		printf(
			'<div class="updated notice is-dismissible"><p>%s</p></div>',
			sprintf( 
				esc_html__( 'Welcome to %s! You can find the plugin at %s.', 'quick-new-site-defaults' ),
				$this->plugin_name,
				$link
			)
		);
		
	}

	/**
	 * Do the admin main function 
	 *
	 * @since    1.0.0
	 *
	 */
	public function main() {

		// get current user ID and settings
		$current_user = wp_get_current_user();
		$this->user_id = $current_user->ID;
	
		$is_valid_submission = ( ! empty( $_POST ) and check_admin_referer( 'quick_set_options', 'quick_set_options_nonce' ) );
		
		// custom strings
		$this->failed_change_label		= __( 'Change failed: %s.', 'quick-new-site-defaults');
		$this->successful_change_label	= __( 'Changed successfully: %s.', 'quick-new-site-defaults');
		
	
		$msgs = array();

		// Delete default posts
		$row_key = 'delete_posts';
		$msgs[ $row_key ][ 'label' ] = esc_html__( 'Delete WordPress default posts', 'quick-new-site-defaults' );
		$msgs[ $row_key ][ 'text' ] = $this->delete_defaultposts();

		// Set option 'Blog Name' by user input
		$row_key = 'blogname';
		$text = 'Site Title';
		$sitetitle_label = esc_html__( $text );
		$msgs[ $row_key ][ 'label' ] = sprintf( '<label for="%s">%s</label>', $row_key, $sitetitle_label );
		$msgs[ $row_key ][ 'text' ] = $this->set_textfield_option( $row_key, $sitetitle_label );

		// Set option 'Blog Description' by user input
		$row_key = 'blogdescription';
		$text = 'Tagline';
		$tagline_label = esc_html__( $text );
		$msgs[ $row_key ][ 'label' ] = sprintf( '<label for="%s">%s</label>', $row_key, $tagline_label );
		$msgs[ $row_key ][ 'text' ] = $this->set_textfield_option( $row_key, $tagline_label );

		/* something goes wrong during storing these values => to debug
		// Set option 'Category Base' to translation of 'category', lower case
		$row_key = 'category_base';
		$text = 'Category';
		$category_label = esc_html( _x( $text, 'taxonomy singular name' ) );
		$text = 'Category base';
		$categorybase_label = esc_html__( $text );
		$msgs[ $row_key ][ 'label' ] = sprintf( '<label for="%s">%s</label>', $row_key, $categorybase_label );
		$msgs[ $row_key ][ 'text' ] = $this->set_permalinkbases( $row_key, $categorybase_label, $category_label );

		// Set option 'Tag Base' to translation of 'tag', lower case
		$row_key = 'tag_base';
		$text = 'Tag';
		$tag_label = esc_html( _x( $text, 'taxonomy singular name' ) );
		$text = 'Tag base';
		$tagbase_label = esc_html__( $text );
		$msgs[ $row_key ][ 'label' ] = sprintf( '<label for="%s">%s</label>', $row_key, $tagbase_label );
		$msgs[ $row_key ][ 'text' ] = $this->set_permalinkbases( $row_key, $tagbase_label, $tag_label );
		*/
		
		// Set blog or CMS
		$row_key = 'set_site_type';
		$text = 'Your homepage displays';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$msgs[ $row_key ][ 'text' ] = $this->set_frontpage();

		// Create default pages
		$row_key = 'create_pages';
		$msgs[ $row_key ][ 'label' ] = esc_html__( 'Create pages', 'quick-new-site-defaults' );
		$msgs[ $row_key ][ 'text' ] = $this->create_pages();
		
		// Remove selected default widgets
		$row_key = 'remove_registered_widgets_by_id';
		$msgs[ $row_key ][ 'label' ] = esc_html__( 'Remove widgets', 'quick-new-site-defaults' );
		$msgs[ $row_key ][ 'text' ] = $this->set_widgets_checkboxes();

		/*
		 * Set blog options
		 *
		 */
		 
		// Post Settings
		$row_key = 'default_post_settings';
		$text = 'Default post settings';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$blogoptions = array();
		$texts = array(
			'default_comment_status'	=> 'Allow people to submit comments on new posts.',
			'default_ping_status'		=> 'Allow link notifications from other blogs (pingbacks and trackbacks) on new posts',
			'default_pingback_flag'		=> 'Attempt to notify any blogs linked to from the post',
			'use_smilies'				=> 'Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display',
		);
		foreach ( $texts as $key => $label ) {
			$blogoptions[ $key ] = __( $label );
		}
		$msgs[ $row_key ][ 'text' ] = $this->set_checkbox_options( $blogoptions );
		
		$row_key = 'mailme_settings';
		$text = 'Email me whenever';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$blogoptions = array();
		$texts = array(
			'comments_notify'	=> 'Anyone posts a comment',
			'moderation_notify'	=> 'A comment is held for moderation',
		);
		foreach ( $texts as $key => $label ) {
			$blogoptions[ $key ] = __( $label );
		}
		$msgs[ $row_key ][ 'text' ] = $this->set_checkbox_options( $blogoptions );

		// Moderation Settings
		$row_key = 'moderation_settings';
		$text = 'Before a comment appears';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$blogoptions = array();
		$texts = array(
			'comment_moderation'	=> 'Comment must be manually approved',
			'comment_whitelist'		=> 'Comment author must have a previously approved comment',
			# 'close_comments_for_old_posts' => 'Automatically close comments on articles older than %s days',
		);
		foreach ( $texts as $key => $label ) {
			$blogoptions[ $key ] = __( $label );
		}
		$msgs[ $row_key ][ 'text' ] = $this->set_checkbox_options( $blogoptions );

		// Comments Settings
		$row_key = 'comments_settings';
		$text = 'Other comment settings';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$blogoptions = array();
		$texts = array(
			'require_name_email'	=> 'Comment author must fill out name and email',
			'comment_registration'	=> 'Users must be registered and logged in to comment',
		);
		foreach ( $texts as $key => $label ) {
			$blogoptions[ $key ] = __( $label );
		}
		if (  ! get_option( 'users_can_register' ) and is_multisite() ) {
			$text = '(Signup has been disabled. Only members of this site can comment.)';
			$blogoptions[ 'comment_registration' ] .= ' ' . __( $text );
		}
		$msgs[ $row_key ][ 'text' ] = $this->set_checkbox_options( $blogoptions );
		
		// Post Settings
		$row_key = 'site-visibility';
		$text = 'Search Engine Visibility';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$blogoptions = array();
		$text = 'Discourage search engines from indexing this site';
		$blogoptions[ 'blog_public' ] = __( $text );
		$msgs[ $row_key ][ 'text' ] = $this->set_checkbox_options( $blogoptions );
		
		/*
		 * Misc. settings
		 *
		 */
		 
		$row_key = 'admin_bar_front';
		$text = 'Toolbar';
		$msgs[ $row_key ][ 'label' ] = esc_html__( $text );
		$blogoptions = array();
		$text = 'Show Toolbar when viewing site';
		$blogoptions[ 'admin_bar_front' ] = esc_html__( $text );
		$msgs[ $row_key ][ 'text' ] = $this->set_checkbox_options( $blogoptions );
		
		// print options page
		include_once( 'partials/admin-display.php' );

		
	}

	/**
	 * Check user privileges and exit on failure with message
	 *
	 * @since    1.0.0
	 *
	 */
	private function check_permissions() {
		$text = '';

		// permission for changing settings
		if ( ! current_user_can( 'manage_options' ) ) {
			$text =  'Sorry, you are not allowed to manage options for this site.';

		// permission for toggle admin bar off
		} elseif ( ! current_user_can( 'edit_user' ) ) {
			$text = 'Sorry, you are not allowed to edit this user.';

		// permission for deleting default post and page ('Hello World' and 'Sample Page')
		} elseif ( ! current_user_can( 'delete_posts' ) ) {
			$text = 'Sorry, you are not allowed to update this site.';

		// permission for creating pages
		} elseif ( ! current_user_can( 'publish_pages' ) ) {
			$text = 'Sorry, you are not allowed to publish this page.';

		// permission for deleting widgets
		} elseif ( ! current_user_can( 'edit_theme_options' ) ) {
			$text = 'Sorry, you are not allowed to edit theme options on this site.';
		}
		
		// if at least one permission is rejected exit with message
		if ( $text ) {
			wp_die( __( $text ) );
		}
	}
	
	/**
	 * Create output for default posts deletion
	 * 
	 * @since    1.0.0
	 */
	private function delete_defaultposts () {
		
		$fields = '';

		// WP translations
		$text = 'Delete %s';
		$delete_label		= _x( $text, 'plugin' );
		$text = 'Post';
		$post_label			= _x( $text, 'post type singular name' );
		$text = 'Page';
		$page_label			= _x( $text, 'post type singular name' );
		$text = '%s post permanently deleted.';
		$deleted_post_label	= __( $text );
		
		// own translations
		$unavailable_post_label	= __( 'Post ID %d permanently deleted.', 'quick-new-site-defaults' );
		$failed_post_label		= __( 'Deletion of post %d failed.', 'quick-new-site-defaults');
		
		// check for values and perform changes

		foreach( array( 1, 2 ) as $post_id ) {
			
			// try to get post object
			$post_obj = get_post( $post_id );
			
			// if post is available
			if ( $post_obj ) {
				
				// get some post properties
				$post_type = ( 'page' == $post_obj->post_type ) ? $page_label : $post_label;
				$post_title = $post_obj->post_title;
				$key = 'delete_post_' . $post_id;
				
				// if post should be deleted
				if ( isset( $_POST[ $key ] ) and '1' == $_POST[ $key ] ) {
					
					// delete post permanently, get return value
					$return = wp_delete_post( $post_id, true );
					// check if failure
					if ( false === $return ) {
						// add error message
						$feedback = sprintf( '<span class="qnsd_error">%s</span>', sprintf( esc_html( $failed_post_label ), $post_id ) );
					} else {
						// add success message
						$feedback = sprintf( '<span class="qnsd_success">%s</span>', sprintf( esc_html( $deleted_post_label ), $post_title ) );
					}
					
				} else {
					
					// post is available for selection
					$fields .= sprintf(
						'<label><input type="checkbox" id="%s" name="%s" value="1" %s> %s <em>%s</em></label>',
						$key,
						$key,
						checked( isset( $_POST[ $key ] ), 1, false ),
						sprintf( esc_html( $delete_label ), $post_type ),
						esc_html( $post_title )
					);
					
				} // if isset $_POST[ $key ]
		
			} else {
				
				// post is not available
				$fields .= sprintf( '<span class="qnsd_notice">%s</span>', sprintf( esc_html( $unavailable_post_label ), $post_id ) );

			} // if post_obj
			
			$fields .= "<br>\n";
			
		} // foreach post_id
		
		return $fields;
	}

	/**
	 * Set selected default widgets inactive
	 *
	 * @since    1.0.0
	 *
	 */
	private function set_widgets_checkboxes() {
		
		$fields = '';
		$changed_sidebar = false;

		// get default widgets
		global $wp_widget_factory;
		$registered_widgets = $wp_widget_factory->widgets;
		// summarize registered widgets by id
		$registered_widgets_by_id = array();
		foreach ( $registered_widgets as $slug => $widget ) {
			$registered_widgets_by_id[ $widget->id ] = $widget;
		}

		// get ids of active default widgets
		$sidebars_widgets = wp_get_sidebars_widgets();
		
		// set list of active default widgets
		if ( is_array( $sidebars_widgets ) ) {

			// initialize default sidebar variable
			$default_sidebar = array();
			$default_active_widgets = array();
			
			// try to find default sidebar with active widgets
			foreach ( $sidebars_widgets as $sidebar_id => $default_active_widgets ) {
				
				if ( 'wp_inactive_widgets' === $sidebar_id || 'orphaned_widgets' === substr( $sidebar_id, 0, 16 ) ) {
					continue;
				}
				
				// take first found sidebar as default and quit loop
				// sidebar id is in $sidebar_id
				// widgets are in $default_active_widgets
				break;
				
			} // foreach ( sidebars_widgets )
			
			if ( ! empty( $_POST ) and isset( $_POST[ 'widgets_to_remove' ] ) ) {
				$widgets_to_remove = $_POST[ 'widgets_to_remove' ];
			} else {
				$widgets_to_remove = array();
			}
			
			// build form fields
			foreach ( $default_active_widgets as $widget_id ) {
				
				// get widget object; check if ID is valid
				if ( isset( $registered_widgets_by_id[ $widget_id ] ) ) {
					$widget = $registered_widgets_by_id[ $widget_id ];
				} else {
					continue;
				}

				// if widget was selected for removal
				if ( in_array( $widget_id, $widgets_to_remove ) ) {

					// slice the widget id at '-' character
					$pieces = explode( '-', $widget_id );
					
					// get the last slice: number
					$multi_number = array_pop( $pieces );
					
					// reconstruct the widget name without number
					$id_base = implode( '-', $pieces );
					
					// get widget options
					$widget_options = get_option( 'widget_' . $id_base );
					
					// delete widget options at number
					unset( $widget_options[ $multi_number ] );
					
					// prepare removal of widget from sidebar
					$index = array_search( $widget_id, $sidebars_widgets[ $sidebar_id ] );
					if ( false !== $index ) {
						// store changed options
						update_option( 'widget_' . $id_base, $widget_options );
						
						// remove widget entry from db object					
						unset( $sidebars_widgets[ $sidebar_id ][ $index ] );
						
						// set text
						$text = sprintf(
							'Removed successfully: Widget %s',
							$widget->name
						);
						$fields .= sprintf(
							'<span class="qnsd_success">%s</span>',
							esc_html( $text )
						);
						$fields .= "<br>\n";
						
						$changed_sidebar = true;
					}
					
				} else {
				
					// set checkbox for selection
					$fields .= sprintf(
						'<label><input type="checkbox" name="widgets_to_remove[]" value="%s"> %s</label>',
						$widget_id,
						esc_html( $widget->name )
					);
					$fields .= "<br>\n";
					
				} // if ( isset( widgets_to_remove[ $widget_id ] ) )
				
			} // foreach ( default_active_widgets )

		} // if ( sidebars_widgets )
		
		if ( $changed_sidebar ) {
			wp_set_sidebars_widgets( $sidebars_widgets );
		}
		
		if ( empty( $fields ) ) {
			$fields = esc_html__( 'All default widgets are removed.', 'quick-new-site-defaults' );
		}
		
		return $fields;
	}
	
	/**
	 * Create output for setting a textfield option
	 * 
	 * @since    1.0.0
	 */
	private function set_textfield_option ( $key, $label ) {
		
		$fields = '';
		$feedback = '';
		if ( isset( $_POST[ $key ] ) ) {
			// make value safe
			$value = sanitize_text_field( $_POST[ $key ] );
			// if new value and stored value are different
			if ( $value != get_option( $key ) ) {
				// store new value
				$return = update_option( $key, $value );
				// check if failure
				if ( false === $return ) {
					// add error message
					$feedback = sprintf( '<span class="qnsd_error">%s</span>', sprintf( esc_html( $this->failed_change_label ), $label ) );
				} else {
					// add success message
					$feedback = sprintf( '<span class="qnsd_success">%s</span>', sprintf( esc_html( $this->successful_change_label ), $label ) );
				}
			}
		} // if $_POST[ $key ]
		// get current value
		$value = get_option( $key, '' );
		// build form element
		$fields .= sprintf(
			'<input type="text" id="%s" name="%s" value="%s" class="regular-text">',
			$key,
			$key,
			esc_attr( $value )
		);
		// add feedback if available
		if ( $feedback ) {
			$fields .= sprintf( '<br><em>%s</em>', $feedback );
		}
		
		return $fields;
	}

	/**
	 * Create output for setting category and tag base
	 * 
	 * @since    1.0.0
	 */
	private function set_permalinkbases ( $key, $base_label, $label ) {
		
		$fields = '';
		$feedback = '';
		
		/**
		* In a subdirectory configuration of multisite, the `/blog` prefix is used by
		* default on the main site to avoid collisions with other sites created on that
		* network. If the `permalink_structure` option has been changed to remove this
		* base prefix, WordPress core can no longer account for the possible collision.
		*/
		$blog_prefix = '';
		$stored_value = get_option( $key );
		$permalink_structure = get_option( 'permalink_structure' );
		if ( is_multisite() and ! is_subdomain_install() and is_main_site() and 0 === strpos( $permalink_structure, '/blog/' ) ) {
			$blog_prefix = '/blog';
			$permalink_structure = preg_replace( '|^/?blog|', '', $permalink_structure );
			$stored_value = preg_replace( '|^/?blog|', '', $stored_value );
		}

		if ( isset( $_POST[ $key ] ) ) {
			// make value safe
			$given_value = sanitize_title( $_POST[ $key ] );
			if ( ! empty( $given_value ) ) {
				$given_value = $blog_prefix . preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $given_value ) );
			}

		// if new value and stored value are different
			if ( $given_value != $stored_value ) {
				// store new value
				$return = update_option( $key, $given_value );
				global $wp_rewrite;
				$wp_rewrite->init();
				// check if failure
				if ( false === $return ) {
					// add error message
					$feedback = sprintf( '<span class="qnsd_error">%s</span>', sprintf( esc_html( $this->failed_change_label ), $base_label ) );
				} else {
					// add success message
					$feedback = sprintf( '<span class="qnsd_success">%s</span>', sprintf( esc_html( $this->successful_change_label ), $base_label ) );
				}
			}
		} // if $_POST[ $key ]
		
		// get current value
		$stored_value = get_option( $key, '' );
		
		// if empty, suggest default value
		if ( '' == $stored_value ) {
			if ( $feedback ) {
				$feedback .= ' ';
			}
			$feedback .= sprintf( '%s /%s', esc_html__( 'example:', 'quick-new-site-defaults' ), sanitize_title( $label ) );
		}
		// build form element
		$fields .= sprintf(
			'<input type="text" id="%s" name="%s" value="%s" class="regular-text code">',
			$key,
			$key,
			esc_attr( $stored_value )
		);
		// add feedback if available
		if ( $feedback ) {
			$fields .= sprintf( '<br><em>%s</em>', $feedback );
		}

		return $fields;
	}

	/**
	 * Create output for setting blog options with checkboxes
	 * 
	 * @since    1.0.0
	 */
	private function set_checkbox_options ( $options ) {
		
		$fields = '';
		$user_meta = get_user_meta( $this->user_id );

		foreach ( $options as $key => $label ) {

			// try to get post object
			$value = get_option( $key );
			
			// if option should be set
			if ( ! empty( $_POST ) ) {
				
				if ( isset( $_POST[ $key ] ) ) {
					
					// activate option if values differ
					switch ( $key ) {
						case 'default_ping_status':
						case 'default_comment_status':
							$return = update_option( $key, 'open' );
							break;
						case 'blog_public':
							$return = update_option( $key, '0' );
							break;
						case 'admin_bar_front':
							$return = update_user_meta( $this->user_id, 'show_admin_bar_front', 'true' );
							break;
						default:
							$return = update_option( $key, '1' );
					} // switch $key
				} else {
					
					// deactivate option
					switch ( $key ) {
						case 'default_ping_status':
						case 'default_comment_status':
							$return = update_option( $key, 'closed' );
							break;
						case 'blog_public':
							$return = update_option( $key, '1' );
							break;
						case 'admin_bar_front':
							$return = update_user_meta( $this->user_id, 'show_admin_bar_front', 'false' );
							break;
						default:
							$return = update_option( $key, '0' );
					} // switch $key
				} // if isset $_POST[ $key ]
				
			} // if not empty $_POST
			
			switch ( $key ) {
				case 'default_ping_status':
				case 'default_comment_status':
					$checked = checked( 'open', get_option( $key ), false );
					$value = 'open';
					break;
				case 'blog_public':
					$checked = checked( '0', get_option( $key ), false );
					$value = '0';
					break;
				case 'admin_bar_front':
					$checked = checked( 'true', get_user_meta( $this->user_id, 'show_admin_bar_front', true ), false );
					$value = '1';
					break;
				default:
					$checked = checked( '1', get_option( $key ), false );
					$value = '1';
			} // switch $key
			
			// post is available for selection
			$fields .= sprintf(
				'<label><input type="checkbox" id="%s" name="%s" value="%s" %s> %s</label>',
				$key,
				$key,
				$value,
				$checked,
				esc_html( $label )
			);
			
			$fields .= "<br>\n";
			
		}
		
		return $fields;
	}

	/**
	 * Create output for page creation
	 * 
	 * @since    1.0.0
	 */
	private function create_pages () {
		
		// initialize variables
		$fields = '';
		$available_pages = array();
		$producible_pages = array();
		$return_messages = array();
		$create_label = esc_html__( 'Create empty page', 'quick-new-site-defaults' );
		$created_label = esc_html__( 'Successfully created: Page %s', 'quick-new-site-defaults' );
		$available_page_label = esc_html__( 'Is available: Page %s', 'quick-new-site-defaults' );
		$with_title_label = esc_html__( 'titled with', 'quick-new-site-defaults' );
		
		// set page headlines
		$pages = array(
			__( 'Contact', 'quick-new-site-defaults' ),
			__( 'Sitemap', 'quick-new-site-defaults' ),
			__( 'Legal Notice', 'quick-new-site-defaults' ),
			__( 'Privacy Police', 'quick-new-site-defaults' ),
			__( 'Youth Protection', 'quick-new-site-defaults' ),
			__( 'Cookie Consent', 'quick-new-site-defaults' ),
			__( 'Conditions Of Use', 'quick-new-site-defaults' ),
			__( 'General Terms And Conditions (GTC)', 'quick-new-site-defaults' ),
			__( 'About Us', 'quick-new-site-defaults' ),
			__( 'FAQ', 'quick-new-site-defaults' ),
			__( 'Help', 'quick-new-site-defaults' ),
			__( 'Consulting', 'quick-new-site-defaults' ),
			__( 'Guidelines For Guest Bloggers', 'quick-new-site-defaults' ),
			__( 'Affiliate Program', 'quick-new-site-defaults' ),
			__( 'Shop', 'quick-new-site-defaults' ),
			__( 'Advertising', 'quick-new-site-defaults' ),
			__( 'Unsubscribe', 'quick-new-site-defaults' ),
			__( 'Connect', 'quick-new-site-defaults' ),
			__( 'Partners', 'quick-new-site-defaults' ),
			__( 'Linking Policy', 'quick-new-site-defaults' ),
		);
		
		// walk through pages
		foreach ( $pages as $page_title ) {
			$key = sanitize_title( $page_title );

			// if creation is desired
			if ( isset( $_POST[ $key ] ) and '1' == $_POST[ $key ] ) {

				// get sanitized user given page title
				if ( isset( $_POST[ $key . '-text' ] ) and ! empty ( $_POST[ $key . '-text' ] ) ) {
					$page_title = sanitize_text_field( $_POST[ $key . '-text' ] );
				}
				
				// set page properties
				$post_data = array(
				 'post_title'     => $page_title,
				 'post_content'   => '',
				 'post_status'    => 'publish',
				 'post_type'      => 'page',
				);

				// create page
				$return = wp_insert_post( $post_data, true );

				// add message
				if ( is_wp_error( $return ) ) {
					
					// add error message
					$return_messages[] = sprintf( '<span class="qnsd_error">%s</span>', esc_html( $return->get_error_message() ) );
					
				} else {
					
					// add success message
					$return_messages[] = sprintf( '<span class="qnsd_success">%s</span>', sprintf( $created_label, esc_html( $page_title ) ) );
					
				} // if is_wp_error
				
			} else {

				if ( isset( $_POST[ $key . '-text' ] ) and ! empty ( $_POST[ $key . '-text' ] ) ) {
					$page_title = sanitize_text_field( $_POST[ $key . '-text' ] );
				}
	
				// check if page is available
				$post_obj = get_page_by_title( $page_title );
				
				if ( $post_obj ) {
					
					// set message of available page
					$available_pages[] = sprintf( '<span class="qnsd_notice">%s</span>', sprintf( $available_page_label, esc_html( $page_title ) ) );
					
				} else {

					// set option available for selection
					$producible_pages[] = sprintf(
						'<label><input type="checkbox" id="%s" name="%s" value="1" %s> %s</label> <label>%s <input type="text" value="%s" class="regular-text"></label>',
						$key,
						$key,
						checked( isset( $_POST[ $key ] ), 1, false ),
						$create_label,
						$with_title_label,
						esc_attr( $page_title )
					);
				
				} // if post_obj

			} // if $_POST[ $key ]

		} // foreach pages
		
		// build string
		if ( ! empty( $return_messages  ) ) { $fields .= implode( "<br>\n", $return_messages ); $fields .= "<br>\n"; }
		if ( ! empty( $available_pages  ) ) { $fields .= implode( "<br>\n", $available_pages ); $fields .= "<br>\n"; }
		if ( ! empty( $producible_pages ) ) { $fields .= implode( "<br>\n", $producible_pages ); }
		
		return $fields;
	}

	/**
	 * Create output for front page creation
	 * 
	 * @since    1.0.0
	 */
	private function set_frontpage () {
		
		$fields = '';

		$page_titles = array();
		$text = 'Homepage';
		$page_titles[] = __( $text );
		$page_titles[] = __( 'Blog', 'quick-new-site-defaults' );

		$frontpage_key = 'frontpage-text';
		$blogpage_key = 'blogpage-text';

		$pages = array(
			$frontpage_key => 'page_on_front',
			$blogpage_key => 'page_for_posts',
		);
		
		$current_front_page_label =  __( 'Current Front Page: %s', 'quick-new-site-defaults' );
		$current_blog_page_label =  __( 'Current Blog Page: %s', 'quick-new-site-defaults' );
		$successful_creation_front_page_label = __( 'Successfully created: Front Page %s', 'quick-new-site-defaults' );
		$successful_creation_blog_page_label = __( 'Successfully created: Blog Page %s', 'quick-new-site-defaults' );
		
		
		// if post and post contains show_on_front:
		if ( isset( $_POST[ 'show_on_front' ] ) ) {

			// if show_on_front is 'page':
			if ( 'page' == $_POST[ 'show_on_front' ] ) {
				
				foreach ( $pages as $key => $option_name ) {
					
					// try to get page
					$post_obj = get_page( get_option( $option_name ) );
					
					// if page is available:
					if ( $post_obj ) {
						
						// print message: page is available
						$label = ( $key == $frontpage_key ) ? $current_front_page_label : $current_blog_page_label ;
						$fields .= sprintf( '<span class="qnsd_notice">%s</span>', sprintf( esc_html( $label ), esc_html( $post_obj->post_title ) ) );
						$fields .= "<br>\n";
						
					} else {
						
						// create page with user-given title
						
						// get sanitized user given page title
						if ( isset( $_POST[ $key ] ) and ! empty ( $_POST[ $key ] ) ) {
							$page_title = sanitize_text_field( $_POST[ $key ] );
						} else {
							$page_title = ( $key == $frontpage_key ) ? $page_titles[ 0 ] : $page_titles[ 1 ];
						}
						
						// set page properties
						$post_data = array(
						 'post_title'     => $page_title,
						 'post_content'   => '',
						 'post_status'    => 'publish',
						 'post_type'      => 'page',
						);

						// create page
						$post_id = wp_insert_post( $post_data, true );

						// add message
						if ( is_wp_error( $post_id ) ) {
							
							// add error message
							$fields .= sprintf( '<span class="qnsd_error">%s</span>', esc_html( $post_id->get_error_message() ) );
							
						} else {
							
							// set front/blog page
							update_option( $option_name, $post_id );
							
							// add success message
							$label = ( $key == $frontpage_key ) ? $successful_creation_front_page_label : $successful_creation_blog_page_label;
							$label = sprintf( $label, $page_title );
							$fields .= sprintf( '<span class="qnsd_success">%s</span>', esc_html( $label ) );
							$fields .= "<br>\n";
							
						} // if is_wp_error

					} // if post_obj
					
				} // foreach $pages as $key => $option_name
					
				// set option show_on_front to 'page'
				update_option( 'show_on_front', 'page' );

				// print message: page is front page
				
			} else {

				// set option show_on_front to 'posts'
				update_option( 'show_on_front', 'posts' );
				foreach ( $pages as $key => $option_name ) {
					update_option( $option_name, '0' );
				}
				
				// print message: posts are front page
				$fields .= sprintf( '<span class="qnsd_success">%s</span>', esc_html__( 'Posts are front page', 'quick-new-site-defaults' ) );
				
			} // if 'page' == $_POST[ 'show_on_front' ]
			
		} else {
			
			// print form field show_on_front posts
			
			$text = 'Your latest posts';
			$latest_post_label = __( $text );
			$text = 'A <a href="%s">static page</a> (select below)';
			$static_page_label = __( $text );

			$options = array( 
				'posts' => $latest_post_label,
				'page' => sprintf( $static_page_label, 'edit.php?post_type=page' )
			);
			
			$show_on_front	= get_option( 'show_on_front' );
			
			foreach ( $options as $key => $label ) {

				$fields .= sprintf(
					'<label><input name="show_on_front" type="radio" value="%s" class="tog" %s>%s</label>',
					$key,
					checked( $key, $show_on_front, false ),
					$label
				);
				$fields .= "<br>\n";
			
			} // foreach options

			// try to get page
			$post_obj = get_page( get_option( $pages[ $frontpage_key ] ) );
			
			// if front page is available:
			if ( $post_obj ) {
				
				// print message: front page is available
				$fields .= sprintf( '<span class="qnsd_notice">%s</span>', sprintf( $current_front_page_label, esc_html( $post_obj->post_title ) ) );
				
			} else {
				
				// print form field front page title
				$fields .= sprintf(
					'<label>%s <input type="text" name="%s" value="%s" class="regular-text"></label>',
					esc_html__( 'Create front page with title', 'quick-new-site-defaults' ),
					$frontpage_key,
					esc_attr( $page_titles[ 0 ] )
				);
				
			}
			$fields .= "<br>\n";
		
			// try to get page
			$post_obj = get_page( get_option( $pages[ $blogpage_key ] ) );
			
			// if front page is available:
			if ( $post_obj ) {
				
				// print message: front page is available
				$fields .= sprintf( '<span class="qnsd_notice">%s</span>', sprintf( esc_html( $current_blog_page_label ), esc_html( $post_obj->post_title ) ) );
				
			} else {
				
				// print form field blog page title
				$fields .= sprintf(
					'<label>%s <input type="text" name="%s" value="%s" class="regular-text"></label>',
					esc_html__( 'Create blog page with title', 'quick-new-site-defaults' ),
					$blogpage_key,
					esc_attr( $page_titles[ 1 ] )
				);
				
			}
		
		} // if isset( $_POST[ 'show_on_front' ]
		
		return $fields;
	}

	/**
	 * Get PayPal locale code
	 * 
	 * @since    1.0.0
	 */
	private function get_paypal_locale () {
		// source: https://developer.paypal.com/docs/classic/archive/buttons/
		// source: http://wpcentral.io/internationalization/
		$paypal_locale = get_locale();
		// if locale is not in registered locale code try to find the nearest match
		if ( ! in_array( $paypal_locale, array( 'en_US', 'en_AU', 'es_ES', 'fr_FR', 'de_DE', 'ja_JP', 'it_IT', 'pt_PT', 'pt_BR', 'pl_PL', 'ru_RU', 'sv_SE', 'tr_TR', 'nl_NL', 'zh_CN', 'zh_HK', 'he_IL' ) ) ) {
			if ( 'ja' == $paypal_locale ) { // japanese language
				$paypal_locale = 'ja_JP';
			} else {
				$language_codes = explode( '_', $paypal_locale );
				// test the language
				switch ( $language_codes[ 0 ] ) {
					case 'en':
						$paypal_locale = 'en_US';
						break;
					case 'nl':
						$paypal_locale = 'nl_NL';
						break;
					case 'es':
						$paypal_locale = 'es_ES';
						break;
					case 'de':
						$paypal_locale = 'de_DE';
						break;
					default:
						$paypal_locale = 'en_US';
				} // switch()
			} // if ('ja')
		} // if !in_array()
	
		return $paypal_locale;
	}

}
