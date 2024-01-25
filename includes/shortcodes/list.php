<?php

namespace MN\Filmmarks\Shortcodes;

use MN\Filmmarks\Model;

function list_films($atts)
{
    $current_user = wp_get_current_user_id();
    $list_name = isset($atts['list_name']) ? $atts['list_name'] : null;
    $filmmarks = Model::get_by_user($current_user, $list_name);

    $html = '';
    foreach ($filmmarks as $filmmark) {
        $film = $filmmark->get_film();
        $html .= apply_filters('mn_filmmark_film', film_template($film), $film);
    }

    return $html;
}

function film_template($film)
{
    ob_start();
?>
    <div class="film"><?= $film->post_title ?></div>
<?php
    return ob_get_clean();
}
