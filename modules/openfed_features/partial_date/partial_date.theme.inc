<?php

/**
 * @file
 * Contains preproces functions for the Partial Date module.
 */
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Prepares variables for a partial date.
 *
 * Default template: partial-date.html.twig
 *
 * @param array $variables:
 *   An array containing the following keys:
 *   date: An array of partial date components.
 *   format: A partial date format entity.
 */
function template_preprocess_partial_date(array &$variables) {
  /** @var \Drupal\partial_date\PartialDateFormatterInterface $formatter */
  $formatter = \Drupal::service('partial_date.formatter');
  /** @var \Drupal\partial_date\Entity\PartialDateFormatInterface $format */
  $format = $variables['format'];

  $variables['date_array'] = $variables['date'];
  $variables['date'] = $formatter->format($variables['date'], $format);

  CacheableMetadata::createFromObject($format)->applyTo($variables);
}

/**
 * Prepares variables for a partial date range.
 *
 * Default template: partial-date-range.html.twig
 *
 * @param array $variables:
 *   An array containing the following keys:
 *   from: An array of partial from date components.
 *   to: An array of partial to date components.
 *   format: A partial date format entity.
 */
function template_preprocess_partial_date_range(array &$variables) {
  /** @var \Drupal\partial_date\Entity\PartialDateFormatInterface $format */
  $format = $variables['format'];

  $variables['from_array'] = $variables['from'];
  $variables['from'] = [
    '#theme' => 'partial_date',
    '#date' => $variables['from_array'],
    '#format' => $format,
  ];

  $variables['to_array'] = $variables['to'];
  $variables['to'] = [
    '#theme' => 'partial_date',
    '#date' => $variables['to_array'],
    '#format' => $format,
  ];

  $variables['separator'] = $format->getSeparator('range');

  CacheableMetadata::createFromObject($format)->applyTo($variables);
}
