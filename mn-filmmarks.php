<?php

namespace MN\Filmmarks;

/**
 * Plugin Name:     MN Filmmarks
 * Plugin URI:      https://github.com/codeccoop/mn-filmmarks
 * Description:     Filmmarks lists for MiradaNativa
 * Author:          Còdec Cooperativa
 * Author URI:      https://www.codeccoop.org
 * Text Domain:     mn-filmmarks
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Mn_Filmmarks
 */

define("MN_FILMMARKS_VERSION", "1.0.0");

require_once('includes/class-model.php');
require_once('includes/shortcodes/ListFilmmarks.php');
require_once('includes/shortcodes/SaveFilmmark.php');

class Plugin
{

    private $shortcodes = [];

    public static function activate()
    {
        Model::create_table();
    }

    public static function deactivate()
    {
    }

    public function init()
    {
        $this->register_shortcodes();
    }

    private function register_shortcodes()
    {
        $this->shortcodes[Shortcodes\SaveFilmmark::$tag] = new Shortcodes\SaveFilmmark();
        $this->shortcodes[Shortcodes\ListFilmmarks::$tag] = new Shortcodes\ListFilmmarks();
    }
}

register_activation_hook(__FILE__, function () {
    Plugin::activate();
});

register_deactivation_hook(__FILE__, function () {
    Plugin::deactivate();
});

add_action('init', function () {
    $plugin = new Plugin();
    $plugin->init();
});
