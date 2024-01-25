<?php

namespace MN\Filmmarks\Shortcodes;

function save_film($atts)
{
    $film_id = $atts['film_id'];
    $user_id = wp_get_current_user_id();

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
