<?php
if( !defined( 'ABSPATH' ) ) exit;

function rest_prepare_fields($data, $post, $request)
{
    $isinclude= '';
    $options = apply_filters('getmiss',$isinclude);
    if (!defined($options)) {
        return;
    }
    $_data = $data->data;
    $post_id = $post->ID;
    if (is_miniprogram() || is_debug()) {
        $post_date = $post->post_date;
        $post_type = $post->post_type;
        $author_id = $post->post_author;
        $author_avatar = get_user_meta($author_id, 'avatar', true);
        $taxonomies = get_object_taxonomies($_data['type']);
        $post_title = $post->post_title;
        $post_views = (int)get_post_meta($post_id, "views", true);
        $post_excerpt = $_data["excerpt"]["rendered"];
        $post_content = $_data["content"]["rendered"];
        $session = isset($request['access_token']) ? $request['access_token'] : '';
        if ($session) {
            $access_token = base64_decode($session);
            $users = MP_Auth::login($access_token);
            if ($users) {
                $user_id = $users->ID;
            } else {
                $user_id = 0;
            }
        } else {
            $user_id = 0;
        }
        $_data["id"]  = $post_id;
        $_data["options"] = $options;
        $_data["date"] = $post_date;
        $_data["week"] = get_wp_post_week($post_date);
        unset($_data['author']);
        $_data["author"]["id"] = $author_id;
        $_data["author"]["name"] = get_the_author_meta('nickname', $author_id);
        if ($author_avatar) {
            $_data["author"]["avatar"] = $author_avatar;
        } else {
            $_data["author"]["avatar"] = get_avatar_url($author_id);
        }
        $_data["author"]["description"] = get_the_author_meta('description', $author_id);
        if (get_post_meta($post_id, "source", true)) {
            $_data["meta"]["source"] = get_post_meta($post_id, "source", true);
        }
        $_data["meta"]["thumbnail"] = apply_filters('post_thumbnail', $post_id);
        $_data["meta"]["views"] = $post_views;
        $_data["meta"]["count"] = mp_count_post_content_text_length(wp_strip_all_tags($post_content));
        $_data["comments"] = apply_filters('comment_type_count', $post_id, 'comment');
        $_data["isfav"] = apply_filters('miniprogram_commented', $post_id, $user_id, 'fav');
        // $_data["favs"] = apply_filters( 'comment_type_count', $post_id, 'fav' );
        $_data["islike"] = apply_filters('miniprogram_commented', $post_id, $user_id, 'like');
        $_data["likes"] = apply_filters('comment_type_count', $post_id, 'like');

        if ($post_type == 'topic') {
            $_data["post_likes"] = apply_filters('comment_type_list', $post_id, 'like');
        }

        if ($taxonomies) {
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($post_id, $taxonomy, array('orderby' => 'term_id', 'order' => 'ASC', 'fields' => 'all'));
                foreach ($terms as $term) {
                    $tax = array();
                    $term_cover = get_term_meta($term->term_id, 'cover', true);
                    $tax["id"] = $term->term_id;
                    $tax["name"] = $term->name;
                    $tax["description"] = $term->description;
                    $tax["cover"] = $term_cover ? $term_cover : wp_miniprogram_option('thumbnail');
                    if ($taxonomy === 'post_tag') {
                        $taxonomy = "tag";
                    }
                    $_data[$taxonomy][] = $tax;
                }
            }
        }
        $_data["title"]["rendered"] = html_entity_decode($post_title);
        $_data["excerpt"]["rendered"] = html_entity_decode(wp_strip_all_tags($post_excerpt));

        if (isset($request['id'])) {
            if (!update_post_meta($post_id, 'views', ($post_views + 1))) {
                add_post_meta($post_id, 'views', 1, true);
            }
            if (is_smart_miniprogram()) {
                $custom_keywords = get_post_meta($post_id, "keywords", true);
                if (!$custom_keywords) {
                    $custom_keywords = "";
                    $tags = wp_get_post_tags($post_id);
                    foreach ($tags as $tag) {
                        $custom_keywords = $custom_keywords . $tag->name . ",";
                    }
                }
                $_data["smartprogram"]["title"] = $_data["title"]["rendered"] . '-' . get_bloginfo('name');
                $_data["smartprogram"]["keywords"] = $custom_keywords;
                $_data["smartprogram"]["description"] = $_data["excerpt"]["rendered"];
                $_data["smartprogram"]["image"] = apply_filters('post_images', $post_id);
                $_data["smartprogram"]["visit"] = array('pv' => $post_views);
                $_data["smartprogram"]["comments"] =  apply_filters('comment_type_count', $post_id, 'comment');
                $_data["smartprogram"]["likes"] = apply_filters('comment_type_count', $post_id, 'like');
                $_data["smartprogram"]["collects"] = apply_filters('comment_type_count', $post_id, 'fav');
            }

            if (!$media_video) {
                $_data["content"]["rendered"] = apply_filters('the_video_content', $post_content);
            }
            $_data["post_likes"] = apply_filters('comment_type_list', $post_id, 'like');
            // $_data["post_favs"] = apply_filters( 'comment_type_list', $post_id, 'fav' );
            // $_data["post_likes"] = apply_filters( 'comment_type_list', $post_id, 'like' );
            if (wp_miniprogram_option("prevnext")) {
                $category = get_the_category($post_id);
                $next = get_next_post($category[0]->term_id, '', 'category');
                $previous = get_previous_post($category[0]->term_id, '', 'category');
                if (!empty($next->ID)) {
                    $_data["next_post"]["id"] = $next->ID;
                    $_data["next_post"]["title"]["rendered"] = $next->post_title;
                    $_data["next_post"]["thumbnail"] = apply_filters('post_thumbnail', $next->ID);
                    $_data["next_post"]["views"] = (int)get_post_meta($next->ID, "views", true);
                }
                if (!empty($previous->ID)) {
                    $_data["prev_post"]["id"] = $previous->ID;
                    $_data["prev_post"]["title"]["rendered"] = $previous->post_title;
                    $_data["prev_post"]["thumbnail"] = apply_filters('post_thumbnail', $previous->ID);
                    $_data["prev_post"]["views"] = (int)get_post_meta($previous->ID, "views", true);
                }
            }
        } else {

            if (!wp_miniprogram_option("post_content")) {
                unset($_data['content']);
            }
            if (wp_miniprogram_option("post_picture")) {
                $_data["pictures"] = apply_filters('post_images', $post_id);
            }
        }
    }
    if (is_miniprogram()) {
        unset($_data['book_chooselibrary']);
        unset($_data['book_choosefilms']);
        unset($_data['book_choosemovie']);
        unset($_data['book_chooseapp']);
        unset($_data['book_choosepro']);
        unset($_data['quot_library']);
        unset($_data['quot_films']);
        unset($_data['quot_audio']);
        unset($_data['categories']);
        unset($_data['tags']);
        unset($_data["_edit_lock"]);
        unset($_data["_edit_last"]);
        unset($_data['featured_media']);
        unset($_data['ping_status']);
        unset($_data['template']);
        unset($_data['slug']);
        unset($_data['status']);
        unset($_data['modified_gmt']);
        unset($_data['post_format']);
        unset($_data['date_gmt']);
        unset($_data['guid']);
        unset($_data['curies']);
        unset($_data['modified']);
        unset($_data['status']);
        unset($_data['comment_status']);
        unset($_data['sticky']);
        unset($_data['_links']);
    }
    $data->data = $_data;
    return $data;
}