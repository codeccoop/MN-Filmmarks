<?php

use WPCT_BM\BookMarkList;

add_action('wp_ajax_wpct_bm_user_lists', 'wpct_bm_user_lists');
add_action('wp_ajax_nopriv_wpct_bm_user_lists', 'wpct_bm_user_lists');
function wpct_bm_user_lists()
{
    check_ajax_referer('ajax-bookmarks');

    $user_id = (int) $_POST['user_id'];
    $post_id = (int) $_POST['post_id'];

    try {
        $lists = BookMarkList::get_by_user($user_id);
    } catch (Exception $e) {
        if ($e->getCode() !== 404) {
            throw $e;
        }
    }

    ob_start(); ?>
    <h4><?= __('Save bookmark', 'wpct-bm') ?></h4>
    <p><?= __('Your lists', 'wpct-bm') ?></p>
    <hr/>
    <ul class="wpct-bm-lists">
        <?php foreach ($lists as $list) : $bookmarked = $list->has_bookmark($post_id); ?>
        <li class="wpct-bm-list" id="<?= $list->id ?>" data-bookmarked="<?= $bookmarked ?>"><?= $list->title ?></li>
        <?php endforeach; ?>
        <?php if (empty($lists)) : ?>
        <li class="wpct-bm-list"><?= __('favorites', 'wpct-bm') ?></li>
        <?php endif; ?>
    </ul>
    <?php

    echo ob_get_clean();
    wp_die();
}
