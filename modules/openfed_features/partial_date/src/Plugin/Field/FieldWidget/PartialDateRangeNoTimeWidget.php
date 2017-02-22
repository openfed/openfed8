<?php

namespace Drupal\partial_date\Plugin\Field\FieldWidget;

/**
 * Provides a widget for Partial Date Range fields without time input.
 *
 * @see \Drupal\partial_date\Plugin\Field\FieldWidget\PartialDateNoTimeWidget
 *
 * @FieldWidget(
 *   id = "partial_date_range_only_widget",
 *   label = @Translation("Partial date range only"),
 *   field_types = {
 *     "partial_date_range",
 *   },
 * )
 */
class PartialDateRangeNoTimeWidget extends PartialDateRangeWidget {

  public static function defaultSettings() {
    $components = array_fill_keys(partial_date_component_keys(), 1);
    unset($components['hour'], $components['minute'], $components['second'], $components['timezone']);
    return array(
      'has_time' => FALSE,
      'components' => $components,
      'components_to' => $components,
    ) + parent::defaultSettings();
  }

}
