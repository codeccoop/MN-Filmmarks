<?php

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


namespace MN\Filmmarks;

class Plugin
{

    public static function activate()
    {
    }

    public static function deactivate()
    {
    }

    public function init()
    {
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
