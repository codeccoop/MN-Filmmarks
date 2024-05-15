<?php

namespace WPCT_BM\Shortcodes;

use Exception;
use WPCT_BM\BookMarkList as Model;

require_once 'Shortcode.php';

class UserLists extends Shortcode
{
    public static $tag = 'wpct_bm_user_lists';

    public function callback($atts, $content, $tag)
    {
        $user_id = isset($atts['user_id']) ? (int) $atts['user_id'] : get_current_user_id();
        if (!$user_id) {
            return '';
        }

        try {
            $lists = Model::get_by_user($user_id);
        } catch (Exception $e) {
            if ($e->getCode() !== 404) {
                return '';
            }

            $list = (new Model(['name' => 'favorites', 'user_id' => $user_id]))->save();
            $lists = [$list];
        }

        $html = '';
        foreach ($lists as $list) {
            $html .= apply_filters('wpct_bm_list_template', $this->template($list), $list);
        }

        return $html;
    }

    private function template($list)
    {
        ob_start(); ?>
        <div class="wpct-bm-list"><?= $list->title ?></div>
        <?php
        return ob_get_clean();
    }
}
