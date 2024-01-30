<?php

use MN\Filmmarks\Model as Filmmark;

add_action('wp_ajax_drop_filmmark', 'mn_ajax_drop_filmmark');
add_action('wp_ajax_nopriv_drop_filmmark', 'mn_ajax_drop_filmmark');


function mn_ajax_drop_filmmark()
{
    check_ajax_referer('ajax-filmmark');
    $user_id = (int) $_POST['user_id'];
    $film_id = (int) $_POST['film_id'];
    try {
        $filmmark = Filmmark::get_by_user_film_id($user_id, $film_id);
    } catch (Exception $e) {
        if ($e->getCode() === 404) {
            throw new Exception('Bad Request', 400);
        }
    }

    $filmmark->remove();
    wp_send_json($filmmark->as_json(), 200);
}
