<?php

namespace Drupal\partial_date\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates a partial date minimum to components constraint.
 */
class PartialDateMinimumToComponentsConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\Core\Field\FieldItemInterface $value */
    if ($value->isEmpty()) {
      return;
    }

    $to = $value->to;

    // Get the file to execute validators.
    $field_storage_definition = $value->getFieldDefinition()->getFieldStorageDefinition();
    $minimum_components = $field_storage_definition->getSetting('minimum_components');
    foreach (partial_date_components() as $name => $label) {
      $required =
        $minimum_components['to']['granularity'][$name]
        || (($name !== 'timezone') && $minimum_components['to']['estimates'][$name]);

      if ($required && empty($to[$name])) {
        $this->context->addViolation('To @component is required', ['@component' => $label]);
      }
    }
  }

}