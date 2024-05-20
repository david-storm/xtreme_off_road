<?php

namespace Drupal\xor_certificate\Form;


use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeForm;

/**
 * Form handler for the node edit forms.
 *
 * @internal
 */
class CertificateForm extends NodeForm {
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    if($this->entity->bundle() !== 'certificate') {
      return $form;
    }

    if ($this->currentUser->id() !== '1') {
        $form['field_date_expiration']['widget']['#access'] = FALSE;
    }

    $form['title']['#disabled'] = TRUE;

    $ajax = [
      'callback' => [$this, 'ajaxForm'],
      'wrapper' => 'kvadro',
      'event' => 'change',
    ];
    $form['field_service']['widget']['#ajax'] = $ajax;
    $form['field_count_kvadro']['widget'][0]['value']['#ajax'] = $ajax;

    $form['total_price'] = [
      '#type' => 'markup',
      '#prefix' => '<div id="kvadro">' . $this->t('Total price') . ': ',
      '#weight' => 100,
      '#suffix' => '</div>',
    ];

    return $form;
  }

  public function ajaxForm(array $form, FormStateInterface $form_state) {
    $count = $form_state->getValue('field_count_kvadro')[0]['value'];
    $term_id = $form_state->getValue('field_service')[0]['target_id'] ?? 0;
    $total = [
      '#type' => 'markup',
      '#prefix' => '<div id="kvadro">' . $this->t('Total price') . ': ',
      '#weight' => 100,
      '#suffix' => '</div>',
    ];
    if ($term_id && $count) {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($term_id);
      $price = $term->get('field_price')->value * $count;
      $total['#markup'] = $price . $this->t('UAH');
    }
    return $total;

  }

}
