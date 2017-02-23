<?php

namespace Drupal\partial_date\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a form element for partial date widget.
 *
 * @FormElement("partial_datetime_element")
 * @author CosminFr
 */
class PartialDateElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#input' => TRUE,
      '#process' => [[get_class($this), 'process']],
      '#default_value' => FALSE,
      '#granularity' => FALSE,
      '#minimum_components' => [],
//        '#estimates' => FALSE,
//        '#estimate_options' => FALSE,
//        '#field_sufix' => '',
      '#increments' => array(),
      '#theme_wrappers' => array(
        'container' => array(
          '#attributes' => array(
            'class' => array('partial-date-element', 'clearfix', 'container-inline'),
          ),
        ),
        'form_element',
      ),
    ];
  }
  
  /**
   * Process callback.
   */
  public static function process(&$element, FormStateInterface $form_state, &$complete_form) {
    $granularity = $element['#granularity'];
//    $estimates = $element['#estimates'];
//    $options = $element['#estimate_options'];
    $fieldSufix = $element['#field_sufix'];
    $increments = $element['#increments'];
    $increments += array(
      'second' => 1,
      'minute' => 1,
    );
    $element['#tree'] = TRUE;
    foreach (partial_date_components() as $key => $label) {
      if (!empty($granularity[$key])) {
        $fieldName = $key . $fieldSufix;
          $element[$key] = array(
            '#title' => $label,
            '#placeholder' => $label,
            '#title_display' => 'invisible',
            '#fieldName' => $fieldName,
            '#value' => empty($element['#value'][$key]) ? '' : $element['#value'][$key],
            '#attributes' => array(
                'class' => array('partial_date_component'),
                'fieldName' => $fieldName,
            ),
          );
        if ($key == 'year') {
          $element[$key]['#type'] = 'textfield';
          $element[$key]['#attributes']['size'] = 5;
        } else {
          $inc = empty($increments[$key]) ? 1 : $increments[$key];
          $blank_option = array('' => $label);
          $element[$key]['#type'] = 'select';
          $element[$key]['#options'] = partial_date_granularity_field_options($key, $blank_option, $inc);
        }
      }
    }
//
//    $css = $element['#component_styles'];
//    foreach (\Drupal\Core\Render\Element::children($element) as $child) {
//      if ($element[$child]['#type'] != 'value') {
//        $element[$child]['#prefix'] = '<div class="partial-date-' . (str_replace('_', '-', $child)) . '" style="' . $css . '">';
//        $element[$child]['#suffix'] = '</div>';
//      }
//    }
    return $element;
  }

}
