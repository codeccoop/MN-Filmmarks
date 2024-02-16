<?php

namespace MN\Filmmarks\Shortcodes;

use Exception;
use MN\Filmmarks\Model;

require_once "Shortcode.php";

class SaveFilmmark extends Shortcode
{
    public static $tag = 'mn_filmmarks_save';

    public function callback($atts, $content, $tag)
    {
        if (!isset($atts['film_id'])) {
            return '';
        }

        $film_id = (int) $atts['film_id'];
        $user_id = get_current_user_id();

        if (!$user_id) {
            return '';
        }
        try {
            $filmmark = Model::get_by_user_film_id($user_id, $film_id);
        } catch (Exception $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
            $filmmark = null;
        }

        $action = $filmmark ? 'drop' : 'save';
        // $literal = $filmmark ? 'Borrar' : 'Guardar';
        ob_start();
        ?>
    <button class="mn-filmmarks__ajax-button" data-action="<?= $action ?>" data-filmid="<?= $film_id ?>" data-userid="<?= $user_id ?>">
        <abbr title="<?= $action ?>">
            <svg viewBox="-40 -50 330 330" class="o-svg-icon icon--sm c-button__icon">
                <path d="M128 60.916C102.993 10.522 21.823 24.954 21.823 89.851c0 64.332 87.629 96.79 106.177 135.485 18.547-38.695 106.185-71.153 106.185-135.485 0-64.835-81.143-79.391-106.185-28.935z"></path>
            </svg>
        </abbr>
    </button>
<?php
            return ob_get_clean();
    }
}
