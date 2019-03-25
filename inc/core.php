<?php

	/***
	 *	2019-03-19
	 */

	namespace r\enquete;
	
	
	class Core {

		// init process
		public static function init(){

			self::load_dependencies();
			
			self::load_textdomain();

		}

		
		// load dependencies
		public static function load_dependencies(){

			require_once( INC .'cpt/core.php' );
			CPT::init();

		}


		// load textdomain
		public static function load_textdomain() {			
			\load_plugin_textdomain( NAME, false, NAME .'/lang' );
		}


	}
