<?php

namespace MN\Filmmarks;

use Exception;

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
require_once('includes/ajax/save-filmmark.php');
require_once('includes/ajax/drop-filmmark.php');

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

    public function on_load()
    {
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function register_shortcodes()
    {
        $this->shortcodes[Shortcodes\SaveFilmmark::$tag] = new Shortcodes\SaveFilmmark();
        $this->shortcodes[Shortcodes\ListFilmmarks::$tag] = new Shortcodes\ListFilmmarks();
    }
    public function enqueue_scripts()
    {
        wp_register_script(
            'mn-ajax-filmmark',
            plugin_dir_url(__FILE__) . 'assets/js/ajax-buttons.js',
            array(),
            MN_FILMMARKS_VERSION,
            true,
        );
        wp_localize_script(
            'mn-ajax-filmmark',
            'ajax_filmmark',
            [
                'nonce' => wp_create_nonce('ajax-filmmark'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ]
        );
        global $post;
        if ($post->post_type === 'film' && is_singular('film')) {
            wp_enqueue_script('mn-ajax-filmmark');
        }
    }
}

register_activation_hook(__FILE__, function () {
    Plugin::activate();
});

register_deactivation_hook(__FILE__, function () {
    Plugin::deactivate();
});

add_action('plugins_loaded', function () {
    $plugin = new Plugin();
    $plugin->on_load();
});
