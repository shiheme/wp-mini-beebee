<?php
if ( !defined( 'ABSPATH' ) ) exit;
include( APP_BEEBEE_REST_API. 'bee-admin/admin/option.php' ); 
include( APP_BEEBEE_REST_API. 'bee-admin/admin/core/options.php' );
include( APP_BEEBEE_REST_API. 'bee-admin/admin/core/menu.php');
include( APP_BEEBEE_REST_API. 'bee-admin/admin/core/spider.php' );
include( APP_BEEBEE_REST_API. 'bee-admin/admin/core/interface.php' );
include( APP_BEEBEE_REST_API. 'bee-admin/admin/core/sanitization.php' );


add_action( 'admin_enqueue_scripts', function () {
	wp_enqueue_style('appbeebee', APP_BEEBEE_API_URL.'bee-admin/static/style.css', array(), get_bloginfo('version') );
	wp_enqueue_script( 'appbeebee', APP_BEEBEE_API_URL.'bee-admin/static/script.js', array( 'jquery' ), get_bloginfo('version') ,true );
	wp_enqueue_script( 'appbeebee-plugin-install', APP_BEEBEE_API_URL.'bee-admin/static/newstyle.js', array( 'jquery' ), get_bloginfo('version') );
	add_thickbox();
} );

add_action( 'admin_init', function() {
	register_setting( "beeapp-group", "beeapp", array( 'sanitize_callback' => 'validate_sanitize_appbeebee_options' ) );
});

add_action( 'admin_notices', function () {
	if( isset($_GET['page']) && trim($_GET['page']) == 'appbeebee' && isset($_REQUEST['settings-updated']) ) {
		$class = 'notice notice-success is-dismissible';
		$message = __( '设置已更新保存!', 'imahui' );
		printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', esc_attr( $class ), esc_html( $message ) );
	}
} );

add_action('admin_footer', function () {
	echo '<script type="text/html" id="tmpl-mp-del-item">
	<a href="javascript:;" class="button del-item">删除</a> <span class="dashicons dashicons-menu"></span>
</script>';
});
