<?php
if (isset($_REQUEST['settings-updated'])) {
	wp_cache_flush();
}
?>
<div class="wrap">
	<h1></h1>
	<div id="poststuff" class="bee-container-card">
		<div id="themechecked-tit"></div>
		<div class="dops-notice is-info"><span class="dops-notice__icon-wrapper"><svg class="gridicon gridicons-info dops-notice__icon" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
					<g>
						<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path>
					</g>
				</svg></span><span class="dops-notice__content">
				<span class="dops-notice__text">配套小程序源码、数据包以及详细配置教程前往关注微信公众号【APP比比】获取。主题升级信息请留意比比交流群公告。
				</span></span><a class="dops-notice__action" href="https://www.appbeebee.com/" target="_blank"><span>了解更多</span></a></div>
		<div id="col-container" class="wp-clearfix">
			<h2 class="title">主题选择</h2>
			<div id="col-wrap">
				<form id="<?php echo $option["id"]; ?>" method="post" action="options.php" enctype="multipart/form-data">
					<?php settings_fields($option['group']); ?>
					<?php appbeebee_options_container($option['options'], $options); ?>
					<?php do_settings_sections($option['group']); ?>
					<?php submit_button(); ?>
				</form>
			</div>
			<p>©2021 设计/开发 @小鱼哥 [微信：gamch2]</p>
		</div>
	</div>
</div><!-- / .wrap -->
<script type="text/javascript">
	jQuery('#<?php echo $option["id"]; ?> input[type=radio][name*=choosetheme]').on('change', function(e) {
		jQuery(this).parents('#<?php echo $option["id"]; ?> ').find('#submit').click();
	})
</script>