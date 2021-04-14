<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', 'beebee_acf_op_init');
function beebee_acf_op_init() {
    if( function_exists('acf_add_options_sub_page') ) {
        $acf_add_options_sub_page = acf_add_options_sub_page(
            array(
                'page_title' => '主题设置',
                'menu_slug' => 'appbeebee-theme-setting',
                'menu_title' => '主题设置',
                'parent_slug'     => 'appbeebee',
                'capability'      => 'manage_options',
                'update_button'   => '保存',
                'updated_message' => '设置已保存！'
            )
        );
    }
} 

add_filter('manage_beebee_library_posts_columns', 'my_edit_beebee_library_columns');
function my_edit_beebee_library_columns($columns)
{
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('书名'),
        'id' => __('ID'),
        'thumbimage' => __('封面'),
        'desc' => __('简述'),
        'beebee_library_state' => __('状态'),
        'beebee_library_cats' => __('分类'),
        'comments' =>  __('评论'),
        'date' => __('Date')
    );
    return $columns;
}

add_action('admin_head', 'add_beebee_library_css');
function add_beebee_library_css()
{
?>
    <style type="text/css">
        .table-view-list .column-thumbimage {
            width: 8%;
        }

        .table-view-list .column-desc {
            width: 20%;
        }

        .table-view-list .column-id {
            width: 6%;
        }

        .table-view-list ol {
            margin: 0 0 0 1.2em;
        }
    </style>
<?php
}

add_action('manage_beebee_library_posts_custom_column', 'my_manage_beebee_library_columns', 10, 2);
function my_manage_beebee_library_columns($column, $post_id)
{
    global $post;
    switch ($column) {
        case 'id':
            $id = get_the_ID($post_id);
            printf(__('%s'),  $id);
            break;

        case 'desc':
            $desc = get_the_excerpt($post_id);
            if (empty($desc))
                echo __('-');
            else
                printf(__('%s'),  $desc);
            break;

        case 'thumbimage':
            /* 得到文章的元数据. */
            $thumbimage = get_the_post_thumbnail_url($post_id, 'thumbnail');
            /* 如果没有数据的时候给出默认显示数据为未知. */
            if (empty($thumbimage))
                echo __('未知');
            /* 如果存在数据的话我们把描述也加上*/
            else
                printf(__('<div style="width:45px;height:63px;border-radius: 3px;background-image:url(%s);background-size: cover;background-repeat: no-repeat;background-position: center;"></div>'), $thumbimage);
            break;
        case 'beebee_library_state':
            $terms = get_the_terms($post_id, 'beebee_library_state');
            if (!empty($terms)) {
                $out = array();
                foreach ($terms as $term) {
                    $out[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(array('post_type' => $post->post_type, 'beebee_library_state' => $term->slug), 'edit.php')),
                        esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'beebee_library_state', 'display'))
                    );
                }
                echo join(', ', $out);
            } else {
                _e('没有状态');
            }
            break;
            case 'beebee_library_cats':
                $terms = get_the_terms($post_id, 'beebee_library_cats');
                if (!empty($terms)) {
                    $out = array();
                    foreach ($terms as $term) {
                        $out[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(array('post_type' => $post->post_type, 'beebee_library_cats' => $term->slug), 'edit.php')),
                            esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'beebee_library_cats', 'display'))
                        );
                    }
                    echo join(', ', $out);
                } else {
                    _e('没有分类');
                }
                break;
        default:
            break;
    }
}
