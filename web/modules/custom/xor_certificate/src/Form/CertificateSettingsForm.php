<?php declare(strict_types=1);


namespace Drupal\xor_certificate\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class CertificateSettingsForm extends ConfigFormBase {

  const SETTINGS = 'xor_certificate.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'xor_certificate_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(static::SETTINGS);

    $form['slot'] = [
      '#type' => 'details',
      '#title' => $this->t('Slot settings'),
      '#open' => TRUE,
    ];
    $form['slot']['count'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Count certification'),
      '#default_value' => $config->get('count'),
    ];
    $form['slot']['size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Size Qr code'),
      '#default_value' => $config->get('size'),
    ];

    $form['slot']['qr_y'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Y Qr code'),
      '#default_value' => $config->get('qr_y'),
    ];
    $form['slot']['qr_x'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X Qr code'),
      '#default_value' => $config->get('qr_x'),
    ];

    $form['slot']['text_y'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Y text code'),
      '#default_value' => $config->get('text_y'),
    ];
    $form['slot']['text_x'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X text code'),
      '#default_value' => $config->get('text_x'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(static::SETTINGS)
      ->set('count', $form_state->getValue('count'))
      ->set('size', $form_state->getValue('size'))
      ->set('qr_y', $form_state->getValue('qr_y'))
      ->set('qr_x', $form_state->getValue('qr_x'))
      ->set('text_y', $form_state->getValue('text_y'))
      ->set('text_x', $form_state->getValue('text_x'))
      ->save();


    $form_state->setRedirect('xor_certificate.generate', ['test' => TRUE]);
    parent::submitForm($form, $form_state);
  }

}
