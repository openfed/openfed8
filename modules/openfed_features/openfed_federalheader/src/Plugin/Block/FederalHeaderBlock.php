<?php

namespace Drupal\openfed_federalheader\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Federal header block.
 *
 * @Block(
 *   id = "federal_header_block",
 *   admin_label = @Translation("Federal header"),
 * )
 */
class FederalHeaderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#theme' => 'federalheader_block',
      '#module_path' => base_path() . drupal_get_path('module', 'openfed_federalheader'),
      '#other_information' => t('Other information and services: <span><a href=":link">www.belgium.be</a></span>', [':link' => 'https://www.belgium.be/' . $langcode]),
      '#attached' => [
        'library' => [
          'openfed_federalheader/header',
        ],
      ],
    ];
  }

}
