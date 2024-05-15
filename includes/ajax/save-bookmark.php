<?php

use WPCT_BM\BookMark;
use WPCT_BM\BookMarkList;

add_action('wp_ajax_wpct_bm_save_bookmark', 'wpct_bm_save_bookmark');
add_action('wp_ajax_nopriv_wpct_bm_save_bookmark', 'wpct_bm_save_bookmark');
function wpct_bm_save_bookmark()
{
    check_ajax_referer('ajax-bookmarks');

    $user_id = (int) $_POST['user_id'];
    $post_id = (int) $_POST['post_id'];
    $list_id = isset($_POST['list_id']) ? $_POST['list_id'] : null;

    if (empty($list_id)) {
        $list_id = (BookMarkList::get_favourite($user_id))->id;
    }

    try {
        BookMark::get_one($user_id, $post_id, $list_id);
        throw new Exception('Bad Request', 400);
    } catch (Exception $e) {
        if ($e->getCode() !== 404) {
            throw $e;
        }
    }

    $bookmark = (new BookMark(
        [
            'user_id' => $user_id,
            'post_id' => $post_id,
            'list_id' => $list_id,
        ]
    ))->save();

    wp_send_json([
        'message' => __('Bookmark saved', 'wpct-bm'),
        'data' => $bookmark->as_json()
    ], 200);
}
