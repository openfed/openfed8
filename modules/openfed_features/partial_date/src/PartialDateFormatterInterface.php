<?php

namespace Drupal\partial_date;

use Drupal\partial_date\Entity\PartialDateFormatInterface;

/**
 * Renders a partial date array into a string using a partial date format.
 */
interface PartialDateFormatterInterface {

  /**
   * Formats a partial date.
   *
   * @param array $date
   *   An array of partial date components.
   * @param \Drupal\partial_date\Entity\PartialDateFormatInterface $format
   *   The partial date format.
   *
   * @return string
   *   The formatted partial date.
   */
  public function format(array $date, PartialDateFormatInterface $format);

}
