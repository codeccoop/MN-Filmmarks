<?php

namespace MN\Filmmarks\Shortcodes;

require_once "Shortcode.php";

class SaveFilmmark extends Shortcode
{
    public static $tag = 'mn_filmmark_save';

    public function __construct()
    {
        parent::__construct();

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

    public function callback($atts, $content, $tag)
    {
        $film_id = $atts['film_id'];
        $user_id = get_current_user_id();

        if (!$user_id) return "";

        wp_enqueue_script("filmmarks-save-buttons");
        ob_start();
?>
        <button class="mn-filmmark-save-button" data-filmid="<?= $film_id ?>" data-userid="<?= $user_id ?>">
            <?= __('Guardar', 'miradanativa') ?>
        </button>
<?php
        return ob_get_clean();
    }
}
