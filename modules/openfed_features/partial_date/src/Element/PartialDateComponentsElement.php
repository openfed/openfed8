<?php

namespace Drupal\partial_date\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a form element for partial date widget.
 *
 * @FormElement("partial_date_components_element")
 * @author CosminFr
 */
class PartialDateComponentsElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#input' => TRUE,
      '#process' => [[get_class($this), 'process']],
      '#tree' => TRUE,
      '#theme_wrappers' => array(
        'container' => array(
          '#attributes' => array(
            'class' => array('partial-date-element', 'clearfix', 'container-inline'),
          ),
        ),
        'form_element',
      ),
      '#options' => partial_date_components(['timezone']),
      '#show_time' => TRUE,
      '#time_states' => FALSE,
    ];
  }
  
  /**
   * Process callback.
   */
  public static function process(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!$element['#show_time']) {
      unset($element['#options']['hour'], $element['#options']['minute'], $element['#options']['second']);
    }
    foreach ($element['#options'] as $key => $label) {
      $element[$key] = array(
        '#type' => 'checkbox',
        '#title' => $label,
        '#value' => in_array($key, $element['#value'], TRUE),
      );
      if ($element['#time_states'] && _partial_date_component_type($key) == 'time') {
        $element[$key]['#states'] = $element['#time_states'];
      }
    }
    return $element;
  }

  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $result = array();
    if ($input === FALSE) {
      $element += array('#default_value' => array());
      $result = $element['#default_value'];
    }
    elseif (is_array($input)) {
      foreach ($input as $key => $value) {
        if (isset($value) && $value != 0) {
          $result[$key] = $value;
        }
      }
    }
    elseif (isset($input)) {
      $result[$input] = $input;
    }
    return array_keys(array_filter($result));
  }
  
}
