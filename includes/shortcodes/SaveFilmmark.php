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
    $literal = $filmmark ? 'Borrar' : 'Guardar';
    ob_start();
?>
    <button class="mn-filmmarks__ajax-button" data-action="<?= $action ?>" data-filmid="<?= $film_id ?>" data-userid="<?= $user_id ?>">
      <?= __($literal, 'miradanativa') ?>
    </button>
<?php
    return ob_get_clean();
  }
}
