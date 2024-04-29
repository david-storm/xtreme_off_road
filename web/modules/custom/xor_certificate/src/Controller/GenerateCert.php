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

  public function __construct() {
    $this->storage = \Drupal::entityTypeManager()->getStorage('node');
  }

  public function content($count = 20) {
    $service = \Drupal::service('fpdi_print.print_builder');
    $pdf = $service->getPdf('', 38);

    $options = new QROptions();
    $options->outputType = QROutputInterface::IMAGICK;
    $options->imagickFormat = 'png32';
    $options->quality = 90;
    $options->eccLevel = EccLevel::M;
    $options->scale = 30;
    $options->bgColor = '#ccccaa';
    $options->imageTransparent = true;
    $options->quietzoneSize = 1;

    for ($i = 0; $i < $count; $i++) {

      $string = $this->string(4, [$this, '_validCertCode']);
      $pdf->SetFont('courier', 'B', 22);
      $pdf->SetTextColor(255, 255, 255);
//      $pdf->setAlpha(0.3);
      $pdf->Cell(12, 12, strtoupper($string));

      $node = $this->storage->create([
        'title' => $string,
        'type' => 'certificate'
      ]);
      $node->save();

      $code = new QRCode($options);
      $fileData = $code->render($node->toUrl(NUll, ['absolute' => True])->toString());
//      $pdf->setAlpha(1);
      $pdf->Image('@' . $fileData, 134.5, 134, 34, 34);

      if ($count - 1 != $i) {
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);
        $pdf->AddPage('L', array($size['width'], $size['height']));
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

