<?php

namespace MN\Filmmarks;

//use Shortcodes\save_film;

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
require_once('includes/shortcodes/list.php');

class Plugin
{


    public static function activate()
    {
        Model::create_table();
    }

    public static function deactivate()
    {
    }

    public static function init()
    {

        add_shortcode('mn_filmmark_list', function ($atts) {
            return \MN\Filmmarks\Shortcodes\list_films($atts);
        });
        add_shortcode('mn_filmmark_save', function ($atts) {
            return Shortcodes\save_film($atts);
        });
        add_action('wp_enqueue_scripts', function () {
            wp_register_script(
                'filmmarks-save-buttons',
                plugin_dir_path(__FILE__) . '/assets/js/save-buttons.js',
                [],
                MN_FILMMARKS_VERSION,
                true
            );
        });
    }
}

register_activation_hook(__FILE__, function () {
    Plugin::activate();
});

register_deactivation_hook(__FILE__, function () {
    Plugin::deactivate();
});

add_action('init', function () {
    Plugin::init();
});
