<?php

namespace Drupal\partial_date\Plugin\DataType;

use Drupal\Core\TypedData\TypedData;

/**
 * Provides a computed partial date property class for partial date fields.
 */
class PartialDateTimeComputed extends TypedData {

  /**
   * An array of date information for the partial date.
   *
   * @var array|null
   */
  protected $partialDate = NULL;

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    if ($this->partialDate !== NULL) {
      return $this->partialDate;
    }

    /** @var \Drupal\Core\Field\FieldItemInterface $item */
    $item = $this->getParent();
    $estimates = $item->getFieldDefinition()->getSetting('estimates');
    $range_setting = $this->definition->getSetting('range');

    $data = $item->data;
    foreach (array_keys(partial_date_components()) as $component) {
      $property = $component;
      if ($range_setting === 'to') {
        $property .= '_to';
      }
      $this->partialDate[$component] = '';
      if (empty($data[$component . '_estimate_from_used'])) {
        $this->partialDate[$component] = $item->{$property};
      }

      $this->partialDate[$component . '_estimate'] = '';
      if ($component !== 'timezone' && !empty($data[$component . '_estimate'])) {
        $value = $data[$component . '_estimate'];

        $this->partialDate[$component . '_estimate'] = $value;
        $this->partialDate[$component . '_estimate_label'] = '';
        $this->partialDate[$component . '_estimate_value'] = NULL;

        if ($value) {
          if (!empty($estimates[$component][$value])) {
            $this->partialDate[$component . '_estimate_label'] = $estimates[$component][$value];
          }
          list($from, $to) = explode('|', $value);
          $this->partialDate[$component . '_estimate_value'] = ($range_setting === 'from') ? $from : $to;
        }
      }
    }
    return $this->partialDate;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    $this->partialDate = $value;
    // Notify the parent of any changes.
    if ($notify && isset($this->parent)) {
      $this->parent->onChange($this->name);
    }
  }

}
