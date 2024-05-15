<?php

use WPCT_BM\BookMark;
use WPCT_BM\BookMarkList;

add_action('wp_ajax_wpct_bm_drop_list', 'wpct_bm_drop_list');
add_action('wp_ajax_nopriv_wpct_bm_drop_list', 'wpct_bm_drop_list');
function wpct_bm_drop_list()
{
    check_ajax_referer('form-bookmarks');

    $list_id = (int) $_POST['list_id'];

    try {
        $list = BookMarkList::get_by_id($list_id);
    } catch (Exception $e) {
        if ($e->getCode() === 404) {
            throw new Exception('Bad Request', 400);
        }
    }

    $list->remove();
    // BookMark::delete_by_list($list->id);
    wp_send_json([
        'message' => __('List removed', 'wpct-bc'),
        'data' => $list->as_json()
    ], 200);
}
