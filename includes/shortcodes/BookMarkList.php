<?php

namespace WPCT_BM\Shortcodes;

use Exception;
use WPCT_BM\BookMarkList as Model;

require_once 'Shortcode.php';

class BookMarkList extends Shortcode
{
    public static $tag = 'wpct_bm_bookmarks';

    public function callback($atts, $content, $tag)
    {
        $user_id = isset($atts['user_id']) ? (int) $atts['user_id'] : get_current_user_id();
        if (!$user_id) {
            return "";
        }

        $list_id = isset($atts['list_id']) ? $atts['list_id'] : null;

        if ($list_id) {
            try {
                $list = Model::get_by_id($list_id);
            } catch (Exception $e) {
                return '';
            }
        } else {
            $list = Model::get_favourite($user_id);
        }

        $html = '';
        foreach ($list->get_bookmarks() as $bookmark) {
            $html .= apply_filters('wpct_bm_bookmark_template', $this->template($bookmark), $bookmark);
        }

        return $html;
    }

    private function template($bookmark)
    {
        $post = get_post($bookmark->post_id);
        ob_start();
        ?>
        <div class="wpct-bm-bookmark"><?= $post->post_title ?></div>
        <?php
        return ob_get_clean();
    }
}
