<?php

use MN\Filmmarks\Model as Filmmark;

add_action('wp_ajax_drop_filmmark', 'mn_ajax_drop_filmmark');
add_action('wp_ajax_nopriv_drop_filmmark', 'mn_ajax_drop_filmmark');


function mn_ajax_drop_filmmark()
{
    check_ajax_referer('ajax-filmmarks');
    $user_id = (int) $_POST['user_id'];
    $film_id = (int) $_POST['film_id'];
    $list_name = isset($_POST['list_name']) ? $_POST['list_name'] : 'favourites';

    $default_lang = pll_default_language();
    $film_id = pll_get_post($film_id, $default_lang);

    try {
        $filmmark = Filmmark::get_by_user_film_id($user_id, $film_id, $list_name);
    } catch (Exception $e) {
        if ($e->getCode() === 404) {
            throw new Exception('Bad Request', 400);
        }
    }

    $filmmark->remove();
    wp_send_json($filmmark->as_json(), 200);
}
