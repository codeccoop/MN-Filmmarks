<?php

namespace MN\Filmmarks\Shortcodes;

use Exception;
use MN\Filmmarks\Model;

require_once 'Shortcode.php';

class ListFilmmarks extends Shortcode
{
    public static $tag = 'mn_filmmark_list';

    public function callback($atts, $content, $tag)
    {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return "";
        }

        $list_name = isset($atts['list_name']) ? $atts['list_name'] : null;

        $filmmarks = Model::get_by_user($user_id, $list_name);
        error_log(print_r($filmmarks, true));

        $html = '';
        foreach ($filmmarks as $filmmark) {
            $film = $filmmark->get_film();
            $html .= apply_filters('mn_filmmark_film', $this->template($film), $film);
        }

        return $html;
    }

    private function template($film)
    {
        ob_start();
?>
        <div class="film"><?= $film->post_title ?></div>
<?php
        return ob_get_clean();
    }
}
