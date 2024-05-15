<?php

use WPCT_BM\BookMark;

add_action('wp_ajax_wpct_bm_drop_bookmark', 'wpct_bm_drop_bookmark');
add_action('wp_ajax_nopriv_wpct_bm_drop_bookmark', 'wpct_bm_drop_bookmark');
function wpct_bm_drop_bookmark()
{
    check_ajax_referer('ajax-bookmarks');

    $user_id = (int) $_POST['user_id'];
    $post_id = (int) $_POST['post_id'];
    $list_id = (int) $_POST['list_id'];

    try {
        $bookmark = BookMark::get_one($user_id, $post_id, $list_id);
    } catch (Exception $e) {
        if ($e->getCode() === 404) {
            throw new Exception('Bad Request', 400);
        }
    }

    $bookmark->remove();
    wp_send_json($bookmark->as_json(), 200);
}
