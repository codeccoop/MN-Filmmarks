<?php

namespace WPCT_BM\Shortcodes;

use Exception;

class Shortcode
{

    public static $tag;

    public function __construct()
    {
        add_shortcode(static::$tag, function ($atts, $content, $tag) {
            $this->enqueue_scripts();
            return $this->callback($atts, $content, $tag);
        });
    }

    private function enqueue_scripts()
    {
        wp_enqueue_script('wpct-bookmarks');
        wp_enqueue_style('wpct-bookmarks');
    }

    public function callback($atts, $content, $tag)
    {
        throw new Exception('To overwrite');
    }
}
