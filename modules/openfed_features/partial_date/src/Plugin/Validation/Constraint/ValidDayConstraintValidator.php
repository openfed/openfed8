<?php

namespace Drupal\partial_date\Plugin\Validation\Constraint;

use Drupal\partial_date\DateTools;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates a valid day constraint.
 */
class ValidDayConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\Core\Field\FieldItemInterface $value */
    if ($value->isEmpty()) {
      return;
    }

    $property = $constraint->property;
    $date = $value->$property;

    $day   = empty($date['day'])   ? 0 : $date['day'];
    $month = empty($date['month']) ? 0 : $date['month'];
    $year  = empty($date['year'])  ? 0 : $date['year'];

    $maxDay = 31;
    $months = DateTools::monthMatrix($year);
    if ($month > 0 && !isset($months[$month - 1])) {
      $maxDay = $months[$month - 1];
    }
    if ($day < 0 || $day > $maxDay) {
      $this->context->addViolation('The specified day is invalid.');
    }
  }

}