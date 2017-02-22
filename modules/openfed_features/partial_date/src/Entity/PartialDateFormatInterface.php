<?php

namespace Drupal\partial_date\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining FormatType config entity
 * 
 * @author CosminFr
 */
interface PartialDateFormatInterface extends ConfigEntityInterface {

  /**
   * Gets the ante meridiem and post meridiem format.
   *
   * @return string
   *   One of the following meridiem formats:
   *   - a: The lowercase meridiem format (am or pm)
   *   - A: The uppercase meridiem format (AM or PM)
   */
  public function getMeridiem();

  /**
   * Gets the year designation format.
   *
   * @return string
   *   One of the following year designation formats:
   *   - sign: Prefixes negative years with a minus
   *   - ad: Suffixes positive years with AD and negative years with BC
   *   - bc: Suffixes negative years with BC
   *   - ce: Suffixes positive years with CE and negative years with BCE
   *   - bce: Suffixes negative years with BCE
   */
  public function getYearDesignation();

  /**
   * Gets the display type for a given component.
   *
   * @param string $component
   *   The date component to return the display type for. Valid components are:
   *   - year
   *   - month
   *   - day
   *   - hour
   *   - minute
   *   - second
   *   - timezone
   *
   * @return string
   *   One of the following display types:
   *   - none: Hides the component
   *   - estimate_label: The label of the estimate
   *   - estimate_range: The range of the estimate
   *   - estimate_component: The start or end date of the estimate
   *   - date_only: The date component
   *   - date_or: The date component that falls back to the estimate label
   */
  public function getDisplay($component);

  /**
   * Gets information about a given component.
   *
   * @param string $component_name
   *   - year
   *   - month
   *   - day
   *   - hour
   *   - minute
   *   - second
   *   - timezone
   *   - approx
   *   - c1
   *   - c2
   *   - c3
   *
   * @return array
   *   An array of format information. If the component is 'year', 'month',
   *   'day', 'hour', 'minute', 'second' or 'timezone', the array contains the
   *   following keys:
   *   - format: A PHP date format string for this component
   *   - empty: An empty text for this component
   *   If the component is 'approx', 'c1', 'c2' or 'c3', the array contains the
   *   following keys:
   *   - value: The value of to be displayed for this component
   *   The following keys are contained for all components:
   *   - weight: The weight of the component
   */
  public function getComponent($component_name);

  /**
   * Gets a sorted list of components.
   *
   * @return array
   *   An array of components keyed by component name. See
   *   PartialDateFormatInterface::getComponent() about a list of component
   *   names and the structure of the component information.
   */
  public function getComponents();

  /**
   * Gets the separator used for a given component.
   *
   * @param string $component
   *   The date component to return the separator for. Valid components are:
   *   - date: The separator to use between different date components
   *   - time: The separator to use between different time components
   *   - datetime: The separator to use between date and time components
   *   - range: The separator to use between components of a date range
   *   - other: The separator to use for other components
   *
   * @return string
   *   The separator for the given component.
   */
  public function getSeparator($component);

}
