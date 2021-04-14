<?php

if (!defined('ABSPATH')) exit;

function register_appbeebee_manage_menu()
{
    add_menu_page(
        '比比小程序原创主题面板',
        '比比小程序',
        'manage_options',
        'appbeebee',
        '',
        'dashicons-icon-appbeebee-logo',
        2
    );

    add_submenu_page('appbeebee', '主题面板', '主题面板', 'manage_options', 'appbeebee', function () {
        appbeebee_options_manage_page();
    });
}

add_action('admin_menu', 'register_appbeebee_manage_menu');

add_action('admin_notices', 'plug_association', 10);
function plug_association()
{
    $out = array();
    $output = '';
    $settings = '';
    $currenttheme = '';
    $display = false;
    $options = apply_filters('appbeebee_setting_options', $path = APP_BEEBEE_REST_API . 'bee-content/themes');
    $option = $options['beebee-theme-choose']['fields']['choosetheme']['options'];
    if (is_admin() && $option) {
        $settings = isset(get_option('beeapp')['choosetheme']) ? get_option('beeapp')['choosetheme'] : '';

        foreach ($option as $key => $option) {
            if ($key == $settings) {
                $currenttheme = $option['title'] . $option['vision'];
                foreach ($option['miss'] as $key => $miss) {
                    if (!defined($key)) {
                        $display = true;
                    }
                    $out[] = sprintf(
                        ' <a href="%s" class="%s" aria-label="%s" data-title="%s">%s</a> %s ',
                        esc_url(network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $miss['url'] . '&TB_iframe=true&width=600&height=550')),
                        "thickbox open-plugin-details-modal open-plugin-details-modal-new",
                        esc_attr($miss['name'] . '的更多信息'),
                        esc_attr($miss['name']),
                        esc_attr($miss['name']),
                        esc_attr($miss['vision'])
                    );
                }
            }
        }

        $output .= '<div class="error notice is-dismissible"><p>您当前启用的小程序主题【<a href="admin.php?page=appbeebee">' . $currenttheme . '</a>】，须安装并启用' . join(__(', '), $out) . '插件</p></div>';
        if ($display) {
            echo $output;
        }
    }
}
