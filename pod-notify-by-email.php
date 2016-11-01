<?php
/*
Plugin Name: Pod Notify By Email 
Plugin URI: http://tremor.se/pod-notify-by-email
Description: Configure each pod with a different email for notification.
Version: v0.1.2
Author: Fredrik Andersson
Author URI: http://tremor.se/
Text Domain: pod-notify-by-email
License: GPL v2 or later
*/

/**
 * Copyright (c) 2016 Fredrik Andersson (email: fredand@gmail.com). All rights reserved.
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
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Loads admin scripts and styles
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add action for pod post save
		add_action( 'pods_api_post_save_pod_item', array( $this, 'pod_post_save' ), 10, 3);

		// Add custom options
		add_filter( 'pods_admin_setup_edit_options', array( $this, 'pt_custom_options' ), 10 , 2 );
				
		// Add tab
		add_filter( 'pods_admin_setup_edit_tabs_post_type', array( $this, 'pt_tab' ), 11, 3 );

		// Add settings for whole plugin
		add_action( 'admin_init', 'pods_settings_api_init' );
		
		require 'plugin-update-checker/plugin-update-checker.php';
		$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
		$myUpdateChecker = new $className(
			'https://github.com/fredand/pod-notify-by-email/',
			__FILE__,
			'master'
		);
		
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
	
	function pt_tab( $tabs, $pod, $addtl_args ) {
		$tabs[ 'pod-notify-by-email' ] = __( 'Notify by email', 'pod-notify-by-email' );
		
		return $tabs;
		
	}
	
	
	function pt_custom_options( $options, $pod  ) {
		$options[ 'pod-notify-by-email' ] = array(
/* Does not work, pod problems (issue: https://github.com/pods-framework/pods/issues/3801)
		'pod_notify_by_email_new_enable' => array(
				'label' => __( 'Notification new pod.' ),
				'help' => __( 'When a new item is created there will be an email sent to this adress. ', 'pod-notify-by-email' ),
				'type' => 'boolean',
				'default' => false,
				'dependency' => true,
				'boolean_yes_label' => 'Yes'
			),
			'pod_notify_by_new_email_adress' => array(
				'label' => __( 'New email.', 'pod-notify-by-email' ),
				'help' => __( 'Send notification to this address when new pod is a submitted.', 'pod-notify-by-email' ),
				'type' => 'text',
				'default' => 'user@email.com',
				'depends-on' => array( 'pod_notify_by_email_new_enable' => true )
			),
				'pod_notify_by_new_email_subject' => array(
				'label' => __( 'New subject.', 'pod-notify-by-email' ),
				'help' => __( 'Notification email subject, you can use some of pods magic-tags (including standard post_title) please oonsult readme for further info.', 'pod-notify-by-email' ),
				'type' => 'text',
				'default' => 'Newly submitted pod: {@post_title}',
				'depends-on' => array( 'pod_notify_by_email_new_enable' => true )
			),
				'pod_notify_by_new_email_body' => array(
				'label' => __( 'New body.', 'pod-notify-by-email' ),
				'help' => __( 'Notification email body, you can use some of pods magic-tags (including standard post_title) please oonsult readme for further info.</br> Note: the magic tag "<b>{@pod_notify_by_email_full_pod_content}</b>" can be used for displaying all submitted data.', 'pod-notify-by-email' ),
				'type' => 'paragraph',
				'default' => '{@pod_notify_by_email_full_pod_content}',
				'depends-on' => array( 'pod_notify_by_email_new_enable' => true )
			),		
*/
			// Updated
			'pod_notify_by_email_updated_enable' => array(
				'label' => __( 'Notification updated pod.', 'pod-notify-by-email' ),
				'help' => __( 'When a podcontent is updated an email will be sent.', 'pod-notify-by-email' ),
				'type' => 'boolean',
				'default' => false,
				'dependency' => true,
				'boolean_yes_label' => 'Yes'
			), 
				'pod_notify_by_updated_email_adress' => array(
				'label' => __( 'Updated email.', 'pod-notify-by-email' ),
				'help' => __( 'Updated email.', 'pod-notify-by-email' ),
				'type' => 'text',
				'default' => 'user@email.com',
				'depends-on' => array( 'pod_notify_by_email_updated_enable' => true )
			),
				'pod_notify_by_updated_email_subject' => array(
				'label' => __( 'Updated subject.', 'pod-notify-by-email' ),
				'help' => __( 'Notification email subject, you can use some of pods magic-tags (including standard post_title) please oonsult readme for further info.', 'pod-notify-by-email' ),
				'type' => 'text',
				'default' => 'Changed pod: {@post_title}',
				'depends-on' => array( 'pod_notify_by_email_updated_enable' => true )
			),
				'pod_notify_by_updated_email_body' => array(
				'label' => __( 'Updated body.', 'pod-notify-by-email' ),
				'help' => __( 'Notification email body, you can use some of pods magic-tags (including standard post_title) please oonsult readme for further info..</br> Note: the magic tag (including standard post_title) "<b>{@pod_notify_by_email_full_pod_content}</b>" can be used for displaying all submitted data.', 'pod-notify-by-email' ),
				'type' => 'paragraph',
				'default' => '{@pod_notify_by_email_full_pod_content}',
				'depends-on' => array( 'pod_notify_by_email_updated_enable' => true )
			)
		);
		
		return $options;
		
	}
	
	function pod_post_save( $pieces, $is_new_item, $id ){
		$pod_notify_by_email_new_enable = $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_email_new_enable' ];
		$pod_notify_by_email_updated_enable = $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_email_updated_enable' ];
		
	
		if ( empty( $pod_notify_by_email_new_enable ) ) { $pod_notify_by_email_new_enable = null; }
		if ( empty( $pod_notify_by_email_updated_enable ) ) { $pod_notify_by_email_updated_enable = null; }
		if ( empty( $is_new_item ) ) { $is_new_item = null; }
		
		if ( $is_new_item ){ // Not working (issue: https://github.com/pods-framework/pods/issues/3801)
			// TODO If $pod_notify_by_email_new_enable = true then execute here.
		} else { 

		if ( $pod_notify_by_email_updated_enable ){
					$email = $this->pod_create_email( $pieces , $is_new_item );
					if( is_email( $email['to'] ) ){
						wp_mail( $email[ 'to' ], $email[ 'subject' ], $email[ 'body' ], null, null );
						add_action( 'admin_notices', 'pod_sentmail_notice' );			
					} else {
						add_action( 'admin_notices', 'pod_no_sentmail_notice' );
					}					
				}
				
		}		
	}
	
	public function pod_create_email ( $pieces, $is_new_item ){
		// Prepare array.
		$email = array('subject'=>'', 'body'=>'', 'to'=>'');
		
		// Get email address from options. 
		if($is_new_item){ // Not working (issue: https://github.com/pods-framework/pods/issues/3801)
			$email['to'] = trim( $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_new_email_adress' ] );
			$sendtosubject = $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_new_email_subject' ]; // set default value
			$sendtobody = $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_new_email_body' ]; // set default value

		} else {
			$email['to'] = trim( $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_updated_email_adress' ] );
			$sendtosubject = $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_updated_email_subject' ]; // set default value
			$sendtobody = $pieces[ 'pod' ][ 'options' ][ 'pod_notify_by_updated_email_body' ]; // set default value
		}
		
		if( is_email( $email['to'] ) ){
			foreach ( $pieces[ 'fields' ] as $fields ){ // Search ang replace magictags, pod fields
				$fullcontent = $fields[ 'name' ] . '=' . $fields[ 'value' ];
				$sendtosubject = str_replace( '{@' . $fields[ 'name' ] . '}' , $fields[ 'value' ] , $sendtosubject );
				$sendtobody = str_replace( '{@' . $fields[ 'name' ] . '}' , $fields[ 'value' ] , $sendtobody );
			}
			
			// Start Standard wordpress fields.
			$supports_title = $pieces[ 'pod' ][ 'options' ][ 'supports_title'];
			if ( empty( $supports_title ) ) { $supports_title = null; }		

			if($supports_title){
				$post_title = get_the_title( $pieces[ 'params' ]->id );
				$sendtosubject = str_replace( '{@post_title}' , $post_title , $sendtosubject );
				$fullcontent .= 'post_title' . '=' . $post_title;
			}
			
			// The post permalink
			$sendtobody = str_replace( '{@permalink}' , get_permalink( $pieces[ 'params' ]->id )  ,$sendtobody);
			// The post Author
			$sendtobody = str_replace( '{@author}' , get_the_author( $pieces[ 'params' ]->id )  ,$sendtobody);
			// The post date
			$sendtobody = str_replace( '{@post_date}' , the_date( $pieces[ 'params' ]->id )  ,$sendtobody);		
			// End Standard wordpress fields.
			
			// Post Id
			$sendtobody = str_replace( '{@post_id}' , $pieces[ 'params' ]->id  , $sendtobody );		
			
			// Check for fullcontent tag {@pod_notify_by_email_full_pod_content}
			$sendtobody = str_replace( '{@pod_notify_by_email_full_pod_content}' , $fullcontent , $sendtobody ); 
			
			$email[ 'subject' ] = $sendtosubject; // Set to values, magic-tags and all.
			$email[ 'body' ] = $sendtobody; // Set to values, magic-tags and all.		
		}
		
		return $email;
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

function pod_no_sentmail_notice() {
    $message = __( 'Email not sent, check email address.', 'pod-notify-by-email' );
	?>
    <div class="updated notice notice-error is-dismissible">
        <p><?php echo printf($message); ?></p>
    </div>
    <?php
}

function pod_sentmail_notice() {
	?>
    <div class="updated notice notice-success  is-dismissible"">
        <p><?php _e('Notification email sent.', 'pod-notify-by-email' ); ?></p>
    </div>
    <?php
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
				<p><?php _e( 'You have activated Pods Extend with the plugin "Pod Notify By Email ", but not the core Pods plugin.', 'pods_extend' ); ?></p>
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

