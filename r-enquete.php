<?php
/*
Plugin Name: rEnquete
Plugin URI: https://www.raphaelramos.com.br/wp/plugins/r-enquete/
Description: A Poll shortcode plugin for WP
Version: 0.1.0
Author: Raphael Ramos
Author URI: https://www.raphaelramos.com.br/
Requires at least: 4.8
Tested up to: 3.4.2
*/

	namespace r\enquete;

	// Exit if accessed directly
	if( !defined( '\ABSPATH' ) ) exit;

	
	// plugin path
	define( __NAMESPACE__ .'\PATH', \plugin_dir_path( __FILE__ ) );

	// plugin url
	define( __NAMESPACE__ .'\URL', \plugin_dir_url( __FILE__ ) );
	
	// plugin name
	define( __NAMESPACE__ .'\NAME', \dirname( \plugin_basename( __FILE__ ) ) );
	
	define( __NAMESPACE__ .'\BASENAME', basename( dirname( __FILE__ ) ) );

	// plugin version
	define( __NAMESPACE__ .'\VERSION', '0.1.0' );
	
	// inc folder
	define( __NAMESPACE__ .'\INC', PATH .'inc/' );

	// base class
	require_once( INC .'core.php' );
	
	/***
	 *	The code that runs during plugin activation.
	 *	This action is documented in includes/class-plugin-name-activator.php
	 */
	function activate_renquete() {
		\do_action( 'r/enquete/activate' );
	}
	\register_activation_hook( __FILE__, 'activate_renquete' );


	/***
	 *	The code that runs during plugin deactivation.
	 *	This action is documented in includes/class-plugin-name-deactivator.php
	 */
	function deactivate_renquete() {
		\do_action( 'r/enquete/deactivate' );
	}
	\register_deactivation_hook( __FILE__, 'deactivate_renquete' );


	/***
	 *	Begins execution of the plugin.
	 */
	Core::init();
