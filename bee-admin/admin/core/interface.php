<?php
if (!defined('ABSPATH')) exit;

function appbeebee_options_container($option_name, $options)
{

	$output = '';
	if ($options) {
		foreach ($options as $key => $option) {
			$output .= '<div id="' . $key . '" class="appbeebee-group">' . "\n";
			$output .= '<p>' . $option["summary"] . '</p>' . "\n";
			$output .= appbeebee_table_options_container($option_name, $option["fields"]);
			$output .= '</div>';
		}
	}

	echo $output;
}

function appbeebee_table_options_container($option_name, $fields)
{

	$output = '';
	$settings = get_option($option_name);
	if ($fields) {
		$output .= '<table class="form-table" cellspacing="0" cellpadding="0"></tbody>';
		foreach ($fields as $var => $field) {

			switch ($field['type']) {

				

				case "gallery":
					$value = isset($settings[$var]) ? $settings[$var] : '';
					$output .= '<tr id="' . $var . '_gallery" class="theme-browser rendered">
										<td>
										<div id="theme-list" class="wp-clearfix">';
					foreach ($field['options'] as $key => $option) {
						$output .= '<div class="theme">
											<div class="theme-screenshot" data-link="' . esc_html($option['link']) . '">
												<img src="' . esc_html($option['screenshot']) . '" alt="">
											</div>
											<span class="more-details">' . esc_html($option['subtit']) . '</span>
											<div class="theme-id-container">
												<div class="theme-name"><span >已选用：</span>' . esc_html($option['title']) . esc_html($option['vision']) . '</div>
												<div class="theme-actions">
													<label class="button activate">
													<input style="display:none;" data-title="' . esc_html($option['title']) . '" data-subtit="' . esc_html($option['subtit']) . '" data-desc="' . esc_html($option['desc']) . '" data-vision="' . esc_html($option['vision']) . '" data-crowd="' . esc_html($option['crowd']) . '" type="radio" name="' . esc_attr($option_name . '[' . $var . ']') . '" value="' . esc_attr($key) . '" ' . checked($value, $key, false) . ' />启用
													</label>
													<a class="tabtap set button button-primary load-customize hide-if-no-customize" href="admin.php?page=appbeebee-theme-setting">设置</a>';
						if($option['viewqrcode']!=''){
							$output .= '<a class="thickbox open-plugin-details-modal open-plugin-details-modal-new view button button-primary load-customize hide-if-no-customize" href="' . esc_html($option['viewqrcode']) . '?TB_iframe=true&width=600&height=550">预览</a>';
						} else {
							$output .= '<a class="view button button-primary load-customize hide-if-no-customize" target="_blank" href="' . esc_html($option['link']) . '">预览</a>';
						}
						$output .= '</div></div></div>';				
									
					}
					if (isset($field['description']) && !empty($field['description'])) {
						$output .= '<p class="description">' . $field['description'] . '</p>';
					}
					$output .= '<div class="theme add-new-theme"><a href="https://www.appbeebee.com/" target="_blank"><div class="theme-screenshot"><span></span></div><div class="theme-name">获取新主题</div></a></div></div></td></tr>';
					break;

				default:
					$rows = isset($field["rows"]) ? $field["rows"] : 4;
					$class = isset($field["class"]) ? 'class="' . $field["class"] . '"' : '';
					$placeholder = isset($field["placeholder"]) ? 'placeholder="' . $field["placeholder"] . '"' : '';
					$value = isset($settings[$var]) ? 'value="' . esc_attr($settings[$var]) . '"' : 'value=""';
					$output .= '<tr id="' . $var . '_text">
								<th><label for="' . $var . '">' . $field["title"] . '</label></th>
								<td>
								<input type="text" id="' . esc_attr($var) . '" name="' . esc_attr($option_name . '[' . $var . ']') . '" ' . $class . ' rows="' . $rows . '" ' . $placeholder . ' ' . $value . ' />';
					if (!isset($field["class"]) && isset($field['description']) && !empty($field['description'])) {
						$output .= '<span class="desc description">' . $field['description'] . '</span>';
					}
					if (isset($field["class"]) && isset($field['description']) && !empty($field['description'])) {
						$output .= '<p class="description">' . $field['description'] . '</p>';
					}
					$output .= '</td></tr>';
					break;
			}
		}

		$output .= '</tbody></table>';
	}

	return $output;
}

function validate_sanitize_appbeebee_options($input)
{
	$clean = array();
	$options = apply_filters('appbeebee_setting_options', $path = APP_BEEBEE_REST_API . 'bee-content/themes');
	if ($options) {
		foreach ($options as $key => $option) {
			$fields = $option["fields"];
			foreach ($fields as $var => $field) {
				if (!isset($var)) {
					continue;
				}
				if (!isset($field['type'])) {
					continue;
				}
				$id = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($var));
				if (has_filter('setting_sanitize_' . $field['type'])) {
					$clean[$id] = apply_filters('setting_sanitize_' . $field['type'], $input[$id], $field);
				}
			}
		}
	}
	do_action('update_setting_validate', $clean);
	return $clean;
}
