<?php

namespace MN\Filmmarks\Shortcodes;

use Exception;

class Shortcode
{

    public static $tag;

    public function __construct()
    {
        add_shortcode(static::$tag, [$this, 'callback']);
    }

    public function callback($atts, $content, $tag)
    {
        throw new Exception('To overwrite');
    }
}
