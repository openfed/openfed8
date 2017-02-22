<?php

namespace Drupal\partial_date\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates a partial date minimum from components constraint.
 */
class PartialDateMinimumFromComponentsConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\Core\Field\FieldItemInterface $value */
    if ($value->isEmpty()) {
      return;
    }

    $from = $value->from;

    // Get the file to execute validators.
    $field_storage_definition = $value->getFieldDefinition()->getFieldStorageDefinition();
    $minimum_components = $field_storage_definition->getSetting('minimum_components');
    foreach (partial_date_components() as $name => $label) {
      $required =
        $minimum_components['from']['granularity'][$name]
        || (($name !== 'timezone') && $minimum_components['from']['estimates'][$name]);

      if ($required && empty($from[$name])) {
        $this->context->addViolation('@component is required', ['@component' => $label]);
      }
    }
    foreach (['txt_short', 'txt_long'] as $property) {
      if ($minimum_components[$property] && !$value->{$property}) {
        $this->context->addViolation('@property is required', ['@property' => $property]);
      }
    }
  }

}