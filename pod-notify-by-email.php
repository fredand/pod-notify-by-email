<?php
/*
Plugin Name: Pod Notify By Email 
Plugin URI: http://example.com/
Description: Configure each pod with a different email for notification.
Version: 0.0.1
Author: Fredrik Andersson
Author URI: http://tremor.se/pod-notify-by-email
Text Domain: pod-notify-by-email
License: GPL v2 or later
*/

/**
 * Copyright (c) YEAR Your Name (email: Email). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Define constants
 *
 * @since 0.0.2
 */
define( 'POD_NOTIFY_BY_EMAIL_SLUG', plugin_basename( __FILE__ ) );
define( 'POD_NOTIFY_BY_EMAIL_URL', plugin_dir_url( __FILE__ ) );
define( 'POD_NOTIFY_BY_EMAIL_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Pod_Notify_By_Email class
 *
 * @class Pod_Notify_By_Email The class that holds the entire Pod_Notify_By_Email plugin
 *
 * @since 0.0.1
 */
class Pod_Notify_By_Email {

	/**
	 * Constructor for the Pod_Notify_By_Email class
	 *
	 * Sets up all the appropriate hooks and actions
	 * within the plugin.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		/**
		 * Plugin Setup
		 */
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Localize our plugin
		add_action( 'init', array( $this, 'localization_setup' ) );

		// Loads frontend scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Loads admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add action for pod post save
		add_action( 'pods_api_post_save_pod_item', array( $this, 'pod_post_save' ), 10, 3);

		// Add action for pod admin options
		add_filter( 'pods_admin_setup_edit_options', array( $this, 'add_admin_options' ), 10 , 2 );
				
	}

	/**
	 * Initializes the Pod_Notify_By_Email() class
	 *
	 * Checks for an existing Pod_Notify_By_Email() instance
	 * and if it doesn't find one, creates it.
	 *
	 * @since 0.0.1
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new Pod_Notify_By_Email();
		}

		return $instance;
		
	}

	/**
	 * Placeholder for activation function
	 *
	 * @since 0.0.1
	 */
	public function activate() {

	}

	/**
	 * Placeholder for deactivation function
	 *
	 * @since 0.0.1
	 */
	public function deactivate() {

	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 0.0.1
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'pod-notify-by-email', false, trailingslashit( POD_NOTIFY_BY_EMAIL_URL ) . '/languages/' );
		
	}

	/**
	 * Enqueue front-end scripts
	 *
	 * Allows plugin assets to be loaded.
	 *
	 * @since 0.0.1
	 */
	public function enqueue_scripts() {

		/**
		 * All styles goes here
		 */
		wp_enqueue_style( 'pod-notify-by-email-styles', trailingslashit( POD_NOTIFY_BY_EMAIL_URL ) . 'css/front-end.css' );

		/**
		 * All scripts goes here
		 */
		wp_enqueue_script( 'pod-notify-by-email-scripts', trailingslashit( POD_NOTIFY_BY_EMAIL_URL ) . 'js/front-end.js', array( ), false, true );


		/**
		 * Example for setting up text strings from Javascript files for localization
		 *
		 * Uncomment line below and replace with proper localization variables.
		 */
		// $translation_array = array( 'some_string' => __( 'Some string to translate', 'pod-notify-by-email' ), 'a_value' => '10' );
		// wp_localize_script( 'pod-notify-by-email-scripts', 'podsExtend', $translation_array ) );
		
	}

	/**
	 * Enqueue admin scripts
	 *
	 * Allows plugin assets to be loaded.
	 *
	 * @since 0.0.1
	 */
	public function admin_enqueue_scripts() {

		/**
		 * All admin styles goes here
		 */
		wp_enqueue_style( 'pod-notify-by-email-admin-styles', plugins_url( 'css/admin.css', __FILE__ ) );

		/**
		 * All admin scripts goes here
		 */
		wp_enqueue_script( 'pod-notify-by-email-admin-scripts', plugins_url( 'js/admin.js', __FILE__ ), array( ), false, true );
		
	}

	/**
	 * Adds an admin tab to Pods editor for all post types
	 *
	 * @param array $tabs The admin tabs
	 * @param object $pod Current Pods Object
	 * @param $addtl_args
	 *
	 * @return array
	 *
	 * @since 0.0.1
	 */
	function pt_tab( $tabs, $pod, $addtl_args ) {
		$tabs[ 'pod-notify-by-email' ] = __( 'Pods Extend Options', 'pod-notify-by-email' );
		
		return $tabs;
		
	}

	function add_admin_options( $options, $pod ){
		$pod_notify_by_email_new_enable = array(
			'label' => __( 'Enable notification of new pod-item.', 'pods' ),
			'help' => __( 'When enabled and a valid email-address is supplied, this will notify the user with the newly submitted pod-information.', 'pods' ),
			'type' => 'boolean',
			'default' => false,
			'dependency' => true,
			'boolean_yes_label' => ''
		);
		$options[ 'admin-ui' ] [ 'pod_notify_by_email_new_enable' ] = $pod_notify_by_email_new_enable;


		$pod_notify_by_new_email_adress = array(
			'label' => __( 'Send notification to this address when new pod is a submitted.', 'pods' ),
			'help' => __( 'When a new item is created there will be an email sent to this adress.', 'pods' ),
			'type' => 'text',
			'default' => 'user@email.com',
			'depends-on' => array( 'pod_notify_by_email_new_enable' => true )
		);
		$options[ 'admin-ui' ] [ 'pod_notify_by_new_email_adress' ] = $pod_notify_by_new_email_adress;
		return $options;
	}
	
	function pod_post_save( $pieces, $is_new_item, $id ){
		if ($is_new_item){
			// Not working
		} else {
			// Not working
		}
		
		$nh_notify_when_new_email = trim($pieces[ 'pod' ][ 'options' ][ 'nh_new_poditem_notify_email']);
		echo('$nh_notify_when_new_email: ' . $nh_notify_when_new_email);
		die('asd');
	}
	

} // Pod_Notify_By_Email

/**
 * Initialize class, if Pods is active.
 *
 * @since 0.0.1
 */
add_action( 'plugins_loaded', 'pods_extend_safe_activate');
function pods_extend_safe_activate() {
	if ( defined( 'PODS_VERSION' ) ) {
		$GLOBALS[ 'Pod_Notify_By_Email' ] = Pod_Notify_By_Email::init();
	}

}


/**
 * Throw admin nag if Pods isn't activated.
 *
 * Will only show on the plugins page.
 *
 * @since 0.0.1
 */
add_action( 'admin_notices', 'pods_extend_admin_notice_pods_not_active' );
function pods_extend_admin_notice_pods_not_active() {

	if ( ! defined( 'PODS_VERSION' ) ) {

		//use the global pagenow so we can tell if we are on plugins admin page
		global $pagenow;
		if ( $pagenow == 'plugins.php' ) {
			?>
			<div class="updated">
				<p><?php _e( 'You have activated Pods Extend, but not the core Pods plugin.', 'pods_extend' ); ?></p>
			</div>
		<?php

		} //endif on the right page
	} //endif Pods is not active

}

/**
 * Throw admin nag if Pods minimum version is not met
 *
 * Will only show on the Pods admin page
 *
 * @since 0.0.1
 */
add_action( 'admin_notices', 'pods_extend_admin_notice_pods_min_version_fail' );
function pods_extend_admin_notice_pods_min_version_fail() {

	if ( defined( 'PODS_VERSION' ) ) {

		//set minimum supported version of Pods.
		$minimum_version = '2.3.18';

		//check if Pods version is greater than or equal to minimum supported version for this plugin
		if ( version_compare(  $minimum_version, PODS_VERSION ) >= 0) {

			//create $page variable to check if we are on pods admin page
			$page = pods_v('page','get', false, true );

			//check if we are on Pods Admin page
			if ( $page === 'pods' ) {
				?>
				<div class="updated">
					<p><?php _e( 'Pods Extend, requires Pods version '.$minimum_version.' or later. Current version of Pods is '.PODS_VERSION, 'pods_extend' ); ?></p>
				</div>
			<?php

			} //endif on the right page
		} //endif version compare
	} //endif Pods is not active

}
