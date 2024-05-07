<?php declare(strict_types=1);


namespace Drupal\xor_certificate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CertificateSearchForm extends FormBase {

  public function getFormId() {
    return 'xor_certificate_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attributes']['id'] = 'xor-certificate-search-form';
    $form['search-block'] = [
      '#type' => 'container',
      '#prefix' => '<div class="search-block">',
      '#suffix' => '</div>',
    ];

    $form['search-block']['search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Filter'),
      '#title_display' => 'invisible',
      '#size' => 30,
      '#placeholder' => $this->t('Enter hash'),
    ];

    $form['search-block']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search certificate'),
      '#size' => 30,
      '#placeholder' => $this->t('Filter by block name'),
//      '#ajax' => [
//        'wrapper' => 'xor-certificate-search-form',
//      ],
    ];

    $form['certificate-block'] = [
      '#type' => 'container',
      '#prefix' => '<div class="certificate-block">',
      '#suffix' => '</div>',
    ];

    $form['certificate-block']['certificate-text'] = [
      '#type' => 'markup',
      '#prefix' => '<div class="certificate-text">',
      '#suffix' => '</div>',
      '#markup' => $this->t('Enter the hash code located in the upper left corner of the certificate in the search field.'),
    ];

    $form['certificate-block']['certificate-image'] = [
      '#type' => 'markup',
      '#markup' => '<div class="certificate-image"></div>',
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $cert = strtolower($form_state->getValue('search'));
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    if (!$storage->loadByProperties([
      'title' => $cert,
      'type' => 'certificate',
      'status' => 1
    ])) {
      $form_state->setErrorByName('search', $this->t('Certificate with this code was not found or is not active.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cert = strtolower($form_state->getValue('search'));
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nodes = $storage->loadByProperties([
      'title' => $cert,
      'type' => 'certificate',
      'status' => 1
    ]);
    foreach ($nodes as $node) {
      $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
      $form_state->disableRedirect(FALSE)->setRebuild(FALSE);
      return;
    }
  }
}
