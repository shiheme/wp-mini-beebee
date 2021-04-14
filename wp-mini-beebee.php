<?php
/*
Plugin Name: Beebee Mini 比比小程序
Plugin URI: https://demo.appbeebee.com/
Description: 这里有很多漂亮的原创的微信小程序模板，完全基于开源的程序打造。关注公众号【APP比比】进行配置教程的学习。
Version: 1.0.0
Author:  hellobeebee
Author URI: https://www.appbeebee.com/
requires at least: 5.5
tested up to: 5.7.0
*/

define('APP_BEEBEE_REST_API', plugin_dir_path(__FILE__));
define('APP_BEEBEE_API_URL', plugin_dir_url(__FILE__));
define('APP_BEEBEE_API_PLUGIN',  __FILE__);
add_action('plugins_loaded', function () {
	include(APP_BEEBEE_REST_API . 'bee-admin/admin/admin.php');
	include(APP_BEEBEE_REST_API . 'bee-include/include/include.php');
	include(APP_BEEBEE_REST_API . 'bee-include/router/router.php');
});

// 为插件添加设置快捷链接
add_filter('plugin_action_links', function ($links, $file) {
	if (plugin_basename(__FILE__) !== $file) {
		return $links;
	}
	$settings_link = '<a href="' . add_query_arg(array('page' => 'appbeebee'), admin_url('admin.php')) . '">' . esc_html__('设置', 'appbeebee') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}, 10, 2);

function appbeebee_options_manage_page( ) {
	$option = array(
		'id' 		=> 'beeapp-form',
		'options'	=> 'beeapp',
		"group"		=> "beeapp-group"
	);
	$options = apply_filters( 'appbeebee_setting_options', $path = APP_BEEBEE_REST_API . 'bee-content/themes' );
	require_once( APP_BEEBEE_REST_API. 'bee-admin/admin/core/settings.php' );
		
}

if ( ! class_exists( 'ACF_To_REST_API' ) ) {

	class ACF_To_REST_API {

		const VERSION = '3.3.2';
		private static $default_request_version = 3;
		private static $request_version;

		private static $instance = null;

		public static function init() {
			self::includes();
			self::hooks();
		}

		protected static function instance() {
			if ( is_null( self::$instance ) ) {
				$class = 'ACF_To_REST_API_V3';
				if ( class_exists( $class ) ) {
					self::$instance = new $class;
				}
			}
			return self::$instance;
		}

		private static function includes() {
				require_once dirname( __FILE__ ) . '/bee-admin/admin/library/rest-api/v3/class-acf-to-rest-api-v3.php';

			if ( self::is_plugin_active( 'all' ) ) {
				self::instance()->includes();
			}
		}

		public static function handle_request_version() {
			if ( is_null( self::$request_version ) ) {
				if ( defined( 'ACF_TO_REST_API_REQUEST_VERSION' ) ) {
					self::$request_version = (int) ACF_TO_REST_API_REQUEST_VERSION;
				} else {
					self::$request_version = (int) get_option( 'acf_to_rest_api_request_version', self::$default_request_version );
				}
			}
			return self::$request_version;
		}

		private static function hooks() {
			add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

			if ( self::is_plugin_active( 'all' ) ) {
				add_action( 'rest_api_init', array( __CLASS__, 'create_rest_routes' ), 10 );
				if ( self::$default_request_version == self::handle_request_version() ) {
					ACF_To_REST_API_ACF_Field_Settings::hooks();
				}
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'missing_notice' ) );
			}
		}

		public static function load_plugin_textdomain() {}

		public static function create_rest_routes() {
			self::instance()->create_rest_routes();
		}

		public static function is_plugin_active( $plugin ) {
			if ( 'rest-api' == $plugin ) {
				return class_exists( 'WP_REST_Controller' );
			} elseif ( 'acf' == $plugin ) {
				return class_exists( 'acf' );
			} elseif ( 'all' == $plugin ) {
				return class_exists( 'WP_REST_Controller' ) && class_exists( 'acf' );
			}

			return false;
		}

		public static function missing_notice() {
			self::instance()->missing_notice();
		}
	}

	add_action( 'plugins_loaded', array( 'ACF_To_REST_API', 'init' ) );

}