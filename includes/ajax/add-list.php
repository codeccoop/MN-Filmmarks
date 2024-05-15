<?php

use WPCT_BM\BookMarkList;

add_action('wp_ajax_wpct_bm_add_list', 'wpct_bm_add_list');
add_action('wp_ajax_nopriv_wpct_bm_add_list', 'wpct_bm_add_list');
function wpct_bm_add_list()
{
    check_ajax_referer('form-bookmarks');

    $user_id = (int) $_POST['user_id'];
    $list_name = (string) $_POST['list_name'];

    if (empty($list_name)) {
        throw new Exception('Bad Request', 400);
    }

    try {
        BookMarkList::get_by_name($user_id, $list_name);
        throw new Exception('Bad Request', 400);
    } catch (Exception $e) {
        if ($e->getCode() !== 404) {
            throw $e;
        }
    }

    $list = (new BookMarkList(
        [
            'user_id' => $user_id,
            'name' => trim($list_name),
        ]
    ))->save();

    wp_send_json($list->as_json(), 200);
}
