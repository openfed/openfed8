<?php

namespace Drupal\partial_date\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a constraint for the minimum from components of a partial date.
 *
 * @Constraint(
 *   id = "PartialDateMinimumFromComponents",
 *   label = @Translation("Partial date minimum from components", context = "Validation"),
 * )
 */
class PartialDateMinimumFromComponentsConstraint extends Constraint {

}
