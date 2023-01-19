<?php

namespace Drupal\product_qr_code;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Product QR code helper service.
 */
class ProductQRCodeGenerator {

  /**
   * Method to generate QR code.
   *
   * @param string $link
   *   This URL.
   * @param int $timestamp
   *   The timestamp.
   */
  public function generateProductQrCode(string $link) {
    $writer = new PngWriter();
    // Create QR code.
    $qrCode = QrCode::create($link)
      ->setEncoding(new Encoding('UTF-8'))
      ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
      ->setSize(300)
      ->setMargin(10)
      ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
      ->setForegroundColor(new Color(0, 0, 0))
      ->setBackgroundColor(new Color(255, 255, 255));

    $result = $writer->write($qrCode);
    return $result;
  }

}
