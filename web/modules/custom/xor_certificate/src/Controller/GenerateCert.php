<?php

namespace Drupal\xor_certificate\Controller;


use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;


/**
 * Returns responses for Embed module routes.
 */
class GenerateCert extends ControllerBase {

  protected EntityStorageInterface $storage;

  protected EntityStorageInterface $storageMedia;

  public function __construct() {
    $this->storage = \Drupal::entityTypeManager()->getStorage('node');
    $this->storageMedia = \Drupal::entityTypeManager()->getStorage('media');
  }

  public function content($test) {
    $config = $this->config('xor_certificate.settings');
    $service = \Drupal::service('fpdi_print.print_builder');

    $medias = $this->storageMedia->loadByProperties(['name' => 'default_certificate']);
    $pdfId = 0;
    foreach ($medias as $media) {
      $pdfId = $media->get('field_media_document')->target_id;
      break;
    }
    if(!$pdfId) {
      throw new \Exception('PDF file is missing');
    }

    $count = (int) $config->get('count');
    $qrY = (float) $config->get('qr_y');
    $sizeQR = (float) $config->get('size');
    $qrX = (float) $config->get('qr_x');
    $textY = (float) $config->get('text_y');
    $textX = (float) $config->get('text_x');

    $pdf = $service->getPdf('', $pdfId);

    $options = new QROptions();
    $options->outputType = QROutputInterface::IMAGICK;
    $options->imagickFormat = 'png32';
    $options->quality = 90;
    $options->eccLevel = EccLevel::M;
    $options->scale = 30;
    $options->bgColor = '#ccccaa';
    $options->imageTransparent = true;
    $options->quietzoneSize = 1;
    $lang = $this->languageManager()->getLanguage($this->config('language.negotiation')->get('selected_langcode'));
    for ($i = 0; $i < $count; $i++) {

      $string = $this->string(4, [$this, '_validCertCode']);
      $pdf->SetFont('courier', 'B', 18);
      $pdf->SetTextColor(255, 255, 255);
      $pdf->Cell($textX, $textY, strtoupper($string));

      $node = $this->storage->create([
        'title' => $string,
        'type' => 'certificate'
      ]);
      $node->save();

      $code = new QRCode($options);
      $url = $node->toUrl(NUll, ['absolute' => True, 'language' => $lang])->toString();
      $fileData = $code->render($url);
      $pdf->Image('@' . $fileData, $qrX, $qrY, $sizeQR, $sizeQR);

      if ($test) {
        $node->delete();
        break;
      }
      if ($count - 1 != $i) {
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);
      }
    }
    return $pdf->Output();
  }

  public function _validCertCode($str) {
    if (ctype_lower($str)) {
      return TRUE;
    }
    if (!$this->storage->loadByProperties([
      'title' => $str,
      'type' => 'certificate'
    ])) {
      return TRUE;
    }

    return FALSE;
  }

  public function string($length = 8, $validator = NULL) {
    do {
      $str = '';
      for ($i = 0; $i < $length; $i++) {
        $str .= chr(mt_rand(97, 122));
      }

      $continue = FALSE;
      if (!$continue && is_callable($validator)) {
        $continue = !call_user_func($validator, $str);
      }
    }
    while ($continue);

    return $str;
  }

}

