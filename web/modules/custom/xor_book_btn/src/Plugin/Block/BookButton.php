<?php

namespace Drupal\xor_book_btn\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Fax' block.
 *
 * @Block(
 *   id = "book_button_block",
 *   admin_label = @Translation("Book button block"),
 * )
 */
class BookButton extends BlockBase {

  public function build() {
    return [
      '#markup' => '<a href="#" class="btn btn-primary ms-lg-5">' . $this->t('Book') . '</a>',
      '#attributes' => ['onclick' => 'jQuery("body > .yButton")[0].click();'],
      '#attached' => [
        'library' => ['xor_book_btn/altegio'],
      ],
    ];
  }
}
