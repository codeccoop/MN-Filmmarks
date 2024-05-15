<?php

namespace WPCT_BM\Shortcodes;

use WPCT_BM\BookMark as Model;

require_once "Shortcode.php";

class BookMark extends Shortcode
{
    public static $tag = 'wpct_bm_bookmark';

    public function callback($atts, $content, $tag)
    {
        if (isset($atts['post_id'])) {
            $post_id = (int) $atts['post_id'];
        } else {
            return '';
        }


        if (!isset($atts['user_id'])) {
            $user_id = get_current_user_id();
        } else {
            $user_id = (int) $atts['user_id'];
        }

        if (!$user_id) {
            return '';
        }

        // $default_lang = pll_default_language();
        // $post_id = pll_get_post($post_id, $default_lang);

        // if (!$post_id) {
        //     return '';
        // }

        $bookmarked = Model::is_bookmarked($user_id, $post_id);

        ob_start();
        ?>
        <button
            class="wpct-bm-bookmark"
            aria-controls="wpct-bm-modal"
            data-postid="<?= $post_id ?>"
            data-userid="<?= $user_id ?>"
            data-bookmarked="<?= $bookmarked ?>"
        >
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="-4 -5 34 34">
                <path d="M 6 2 C 5.861875 2 5.7278809 2.0143848 5.5976562 2.0410156 C 4.686084 2.2274316 4 3.033125 4 4 L 4 22 L 12 19 L 20 22 L 20 4 C 20 3.8625 19.985742 3.7275391 19.958984 3.5976562 C 19.799199 2.8163086 19.183691 2.2008008 18.402344 2.0410156 C 18.272119 2.0143848 18.138125 2 18 2 L 6 2 z"></path>
            </svg>
        </button>
        <?php
            return ob_get_clean();
    }
}
