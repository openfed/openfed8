<?php

namespace Drupal\partial_date\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;
use Drupal\partial_date\DateTools;
use Drupal\partial_date\Plugin\DataType\PartialDateTimeComputed;

/**
 * Plugin implementation of the 'partial_date_range' field type.
 *
 * @FieldType(
 *   id = "partial_date_range",
 *   label = @Translation("Partial date and time range"),
 *   description = @Translation("This field stores and renders partial date ranges."),
 *   default_widget = "partial_date_range_widget",
 *   default_formatter = "partial_date_range_formatter",
 * )
 */
class PartialDateTimeRangeItem extends PartialDateTimeItem {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $minimum_components = $field_definition->getSetting('minimum_components');

    $properties['timestamp_to'] = DataDefinition::create('float')
      ->setLabel(t('End timestamp'))
      ->setDescription('Contains the best approximation for end value of the partial date');

    foreach (partial_date_components() as $key => $label) {
      if ($key == 'timezone') {
        continue;
      }

      $properties[$key . '_to'] = DataDefinition::create('integer')
        ->setLabel($label. t(' end '))
        ->setDescription(t('The ' . $label . ' for the finishing date component.'))
        ->setRequired($minimum_components['to']['granularity'][$key]);
    }

    $properties['to'] = MapDataDefinition::create()
      ->setLabel(t('To'))
      ->setClass(PartialDateTimeComputed::class)
      ->setSetting('range', 'to')
      ->setComputed(TRUE);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field) {
    $schema = parent::schema($field);

    $schema['columns']['timestamp_to'] = [
      'type' => 'float',
      'size' => 'big',
      'description' => 'The calculated timestamp for end date stored in UTC as a float for unlimited date range support.',
      'not null' => TRUE,
      'default' => 0,
      'sortable' => TRUE,
    ];
    $schema['indexes']['by_end'] = ['timestamp_to'];

    foreach (partial_date_components() as $key => $label) {
      if ($key == 'timezone') {
        continue;
      }

      $column = $schema['columns'][$key];
      //Add "*_to" columns
      $column['description'] = 'The ' . $label . ' for the finishing date component.';
      $schema['columns'][$key . '_to'] = $column;
    }
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = $this->getTypedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $constraints[] = $constraint_manager->create('PartialDateMinimumToComponents', []);
    $constraints[] = $constraint_manager->create('ComplexData', [
      'year_to' => [
        'Range' => ['min' => DateTools::YEAR_MIN, 'max' => DateTools::YEAR_MAX],
      ],
      'month_to' => [
        'Range' => ['min' => 0, 'max' => 12],
      ],
      'hour_to' => [
        'Range' => ['min' => 0, 'max' => 23],
      ],
      'minute_to' => [
        'Range' => ['min' => 0, 'max' => 59],
      ],
      'second_to' => [
        'Range' => ['min' => 0, 'max' => 59],
      ],
    ]);
    $constraints[] = $constraint_manager->create('ValidDay', [
      'property' => 'to',
    ]);

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    parent::preSave();

    $data = $this->data;

    // Provide some default values for the timestamp components. Components with
    // actual values will be replaced below.
    $timestamp_components = [
      'year' => DateTools::YEAR_MAX,
      'month' => 12,
      // A sensible default for this will be generated below using the month.
      'day' => 0,
      'hour' => 23,
      'minute' => 59,
      'second' => 59,
      'timezone' => '',
    ];
    foreach (array_keys(partial_date_components()) as $component) {
      $property = $component . '_to';
      $to = $this->to;
      if (isset($to[$component])) {
        $this->{$property} = $to[$component];
      }

      if ($component !== 'timezone') {
        $data[$component . '_estimate_to_used'] = FALSE;

        // The if-statements are broken up because $from_estimate_from is used
        // below even if $to[$component] is not empty.
        if (!empty($from[$component . '_estimate'])) {
          list($from_estimate_from, $from_estimate_to) = explode('|', $from[$component . '_estimate']);
          if (!isset($to[$component]) || !strlen($to[$component])) {
            $this->{$property} = $from_estimate_to;
            $data[$component . '_estimate_to_used'] = TRUE;
          }
        }

        $data[$component . '_to_estimate'] = '';
        if (!empty($to[$component . '_estimate'])) {
          $estimate = $to[$component . '_estimate'];
          $data[$component . '_to_estimate'] = $estimate;
          list($to_estimate_from, $to_estimate_to) = explode('|', $estimate);
          if (!isset($from[$component]) || !strlen($from[$component]) || $data[$component . '_estimate_from_used']) {
            $this->{$component} = isset($from_estimate_from) ? min($from_estimate_from, $to_estimate_from) : $to_estimate_from;
            $data[$component . '_estimate_from_used'] = TRUE;
          }
          if (!isset($to[$component]) || !strlen($to[$component]) || $data[$component . '_estimate_to_used']) {
            $this->{$property} = isset($from_estimate_to) ? min($from_estimate_to, $to_estimate_to) : $to_estimate_to;
            $data[$component . '_estimate_to_used'] = TRUE;
          }
        }
      }

      // Build up components for the timestamp to use.
      $value = $this->{$property};
      if ($value && strlen($value)) {
        $timestamp_components[$component] = $value;
      }
    }
    if (!$timestamp_components['day']) {
      $month_table = DateTools::monthMatrix($timestamp_components['year']);
      if (isset($month_table[$timestamp_components['month'] - 1])) {
        $timestamp_components['day'] = $month_table[$timestamp_components['month'] - 1];
      }
      else {
        $timestamp_components['day'] = 31;
      }
    }
    $this->timestamp_to = partial_date_float($timestamp_components);
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = parent::storageSettingsForm($form, $form_state, $has_data);
    $minimum_components = $this->getSetting('minimum_components');
    foreach (partial_date_components() as $key => $label) {
      $elements['minimum_components']['from']['granularity'][$key]['#title'] = t('From @date_component', array('@date_component' => $label));
    }
    foreach (partial_date_components(array('timezone')) as $key => $label) {
      $elements['minimum_components']['from']['estimates'][$key]['#title'] = t('From Estimate @date_component', array('@date_component' => $label));
    }
    foreach (partial_date_components() as $key => $label) {
      $elements['minimum_components']['to']['granularity'][$key] = array(
        '#type' => 'checkbox',
        '#title' => t('To @date_component', array('@date_component' => $label)),
        '#default_value' => $minimum_components['to']['granularity'][$key],
      );
    }
    foreach (partial_date_components(array('timezone')) as $key => $label) {
      $elements['minimum_components']['to']['estimates'][$key] = array(
        '#type' => 'checkbox',
        '#title' => t('To Estimate @date_component', array('@date_component' => $label)),
        '#default_value' => $minimum_components['to']['estimates'][$key],
      );
      if (_partial_date_component_type($key) === 'time') {
        $element['minimum_components']['to']['estimates'][$key] = array(
          'visible' => array(
            ':input[id="has_time"]' => array('checked' => TRUE),
          ),
        );
      }
    }
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    $settings = parent::defaultStorageSettings();
    $settings['minimum_components']['to'] = $settings['minimum_components']['from'];
    return $settings;
  }

}
