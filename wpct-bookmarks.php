<?php

/**
 * Plugin Name:     Wpct BookMarks
 * Plugin URI:      https://git.coopdevs.org/codeccoop/wp/plugins/wpct-bookmarks
 * Description:     WordPress bookmarks
 * Author:          Còdec Cooperativa
 * Author URI:      https://www.codeccoop.org
 * Text Domain:     wpct-bm
 * Domain Path:     /languages
 * Version:         1.0.0
 */

namespace WPCT_BM;

use Exception;

define("WPCT_BM_VERSION", "1.0.0");

require_once('includes/class-model-bookmark.php');
require_once('includes/class-model-list.php');

require_once('includes/shortcodes/BookMarkList.php');
require_once('includes/shortcodes/BookMark.php');
require_once('includes/shortcodes/UserLists.php');

require_once('includes/ajax/add-list.php');
require_once('includes/ajax/drop-list.php');
require_once('includes/ajax/save-bookmark.php');
require_once('includes/ajax/drop-bookmark.php');
require_once('includes/ajax/user-lists.php');

class Plugin
{
    private $shortcodes = [];

    public static function activate()
    {
        BookMarkList::create_table();
        BookMark::create_table();
    }

    public static function deactivate()
    {
    }

    public function on_load()
    {
        add_action('init', [$this, 'register_shortcodes']);
        // add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('init', [$this, 'register_scripts']);
    }

    public function register_shortcodes()
    {
        $this->shortcodes[Shortcodes\BookMark::$tag] = new Shortcodes\BookMark();
        $this->shortcodes[Shortcodes\BookMarkList::$tag] = new Shortcodes\BookMarkList();
        $this->shortcodes[Shortcodes\UserLists::$tag] = new Shortcodes\UserLists();
    }

    public function register_scripts()
    {
        wp_register_script(
            'wpct-bookmarks',
            plugin_dir_url(__FILE__) . 'assets/js/index.js',
            array(),
            WPCT_BM_VERSION,
        );

        wp_localize_script(
            'wpct-bookmarks',
            'ajaxBookmarks',
            [
                'nonce' => wp_create_nonce('ajax-bookmarks'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ]
        );

        wp_register_style(
            'wpct-bookmarks',
            plugin_dir_url(__FILE__) . 'assets/css/index.css',
            [],
            WPCT_BM_VERSION,
        );
    }
}

add_action('wp_head', function () {
    ?>
    <style>.wpct-bm-bookmark{visibility:hidden;pointer-events:none;}</style>
    <?php
});

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

add_action('wpct_bm_drop_list', function ($list_id) {
    BookMark::delete_by_list($list_id);
}, 10, 1);

add_action('delete_user', function ($user_id) {
    BookMark::delete_by_user($user_id);
    BookMarkList::delete_by_user($user_id);
}, 10, 1);

add_action('delete_post', function ($post_id) {
    try {
        BookMark::delete_by_post($post_id);
    } catch (Exception) {
        // do nothing
    }
}, 10, 1);

add_filter('wpct_bm_user_lists', function ($lists, $user_id) {
    return BookMarkList::get_by_user($user_id);
}, 10, 2);

add_action('init', function () {
    load_plugin_textdomain('wpct-bm', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

add_filter('load_textdomain_mofile', function ($mofile, $domain) {
    if ('wpct-bm' === $domain && false !== strpos($mofile, WP_LANG_DIR . '/plugins/')) {
        $locale = apply_filters('plugin_locale', determine_locale(), $domain);
        $mofile = WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}, 10, 2);
