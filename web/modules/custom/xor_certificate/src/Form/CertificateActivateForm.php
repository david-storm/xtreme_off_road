<?php

namespace Drupal\xor_certificate\Form;


use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the node edit forms.
 *
 * @internal
 */
class CertificateActivateForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure want to activate this certificate?');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $node = $this->getEntity();

    if ($node->get('moderation_state')->value === 'published') {
      $node->set('moderation_state', 'activated');
      $node->save();
      \Drupal::messenger()->addMessage(t('Certificate activated!'));
    }
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->getEntity()->toUrl();
  }

}
