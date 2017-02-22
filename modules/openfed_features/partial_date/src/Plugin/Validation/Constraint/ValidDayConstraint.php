<?php

namespace Drupal\partial_date\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a constraint for a valid day.
 *
 * @Constraint(
 *   id = "ValidDay",
 *   label = @Translation("Valid day", context = "Validation"),
 * )
 */
class ValidDayConstraint extends Constraint {

  /**
   * The name of the property that holds the partial date components.
   *
   * @var string
   */
  public $property;

}
