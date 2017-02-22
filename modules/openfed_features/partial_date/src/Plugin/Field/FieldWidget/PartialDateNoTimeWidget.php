<?php

namespace Drupal\partial_date\Plugin\Field\FieldWidget;

/**
 * Provides a widget for Partial Date fields without time input.
 *
 * @see \Drupal\partial_date\Plugin\Field\FieldWidget\PartialDateRangeNoTimeWidget
 *
 * @FieldWidget(
 *   id = "partial_date_only_widget",
 *   label = @Translation("Partial date only"),
 *   field_types = {
 *     "partial_date",
 *   },
 * )
 */
class PartialDateNoTimeWidget extends PartialDateWidget {

  public static function defaultSettings() {
    $components = array_fill_keys(partial_date_component_keys(), 1);
    unset($components['hour'], $components['minute'], $components['second'], $components['timezone']);
    return array(
      'has_time' => FALSE,
      'components' => $components,
    ) + parent::defaultSettings();
  }

}
